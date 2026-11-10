<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Category;
use App\Models\ModerationLog;
use App\Models\Question;
use App\Models\Report;
use App\Models\ReputationTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboards.admin', [
            'stats' => [
                'users' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'questions' => Question::count(),
                'answers' => Answer::count(),
                'pending' => Question::where('status', 'pending')->count(),
                'reports' => Report::where('status', 'pending')->count(),
                'reputation' => User::sum('reputation'),
            ],
            'recentQuestions' => Question::with('author')->latest()->take(8)->get(),
            'recentLogs' => ModerationLog::with('moderator')->latest()->take(6)->get(),
            'categories' => Category::count(),
        ]);
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['users', 'questions', 'answers', 'activity'], true), 404);

        [$headers, $rows] = match ($type) {
            'users' => [['id', 'name', 'email', 'role', 'status', 'reputation', 'created_at'], User::query()->orderBy('id')->lazy()->map(fn (User $user) => [$user->id, $user->name, $user->email, $user->role, $user->status, $user->reputation, $user->created_at])],
            'questions' => [['id', 'title', 'author', 'category', 'status', 'views', 'created_at'], Question::with(['author', 'category'])->orderBy('id')->lazy()->map(fn (Question $question) => [$question->id, $question->title, $question->author->name, $question->category->name, $question->status, $question->views, $question->created_at])],
            'answers' => [['id', 'question_id', 'author', 'status', 'body', 'created_at'], Answer::with('author')->orderBy('id')->lazy()->map(fn (Answer $answer) => [$answer->id, $answer->question_id, $answer->author->name, $answer->status, $answer->body, $answer->created_at])],
            default => [['user_id', 'actor_id', 'reason', 'points', 'event_key', 'created_at'], ReputationTransaction::orderBy('id')->lazy()->map(fn (ReputationTransaction $transaction) => [$transaction->user_id, $transaction->actor_id, $transaction->reason, $transaction->points, $transaction->event_key, $transaction->created_at])],
        };

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, array_map($this->safeCsvValue(...), $row));
            }
            fclose($output);
        }, 'student-qa-'.$type.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function safeCsvValue(mixed $value): string
    {
        $text = (string) $value;

        return preg_match('/^[\s]*[=+@\-]/u', $text) ? "'".$text : $text;
    }
}
