<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ModerationLog;
use App\Models\Question;
use App\Models\Report;
use App\Notifications\PlatformNotification;
use App\Services\ReputationService;
use App\Services\VotingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboards.moderator', [
            'pendingQuestions' => Question::where('status', 'pending')->count(),
            'pendingReports' => Report::where('status', 'pending')->count(),
            'recentLogs' => ModerationLog::with(['moderator', 'moderatable'])->latest()->take(10)->get(),
        ]);
    }

    public function queue(): View
    {
        return view('moderation.queue', [
            'questions' => Question::with(['author', 'category'])->where('status', 'pending')->oldest()->paginate(15, ['*'], 'questions'),
            'reports' => Report::with(['reporter', 'reportable'])->where('status', 'pending')->oldest()->paginate(15, ['*'], 'reports'),
        ]);
    }

    public function question(Request $request, Question $question, string $decision): RedirectResponse
    {
        abort_unless(in_array($decision, ['approve', 'reject'], true), 404);
        abort_unless($question->status === 'pending', 409);
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($request, $question, $decision, $data): void {
            $question->update(['status' => $decision === 'approve' ? 'published' : 'rejected']);
            $question->moderationLogs()->create([
                'moderator_id' => $request->user()->id,
                'action' => 'question_'.$decision,
                'note' => $data['note'] ?? null,
            ]);
        });
        $question->author->notify(new PlatformNotification(
            $decision === 'approve' ? __('messages.question_approved') : __('messages.question_rejected'),
            route('questions.show', $question), 'moderation'));

        return back()->with('success', __('messages.moderation_saved'));
    }

    public function report(Request $request, Report $report, string $decision, ReputationService $reputation, VotingService $voting): RedirectResponse
    {
        abort_unless(in_array($decision, ['dismiss', 'hide'], true), 404);
        abort_unless($report->status === 'pending', 409);
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($request, $report, $decision, $data, $reputation, $voting): void {
            $content = $report->reportable;
            if ($decision === 'hide') {
                $content->update(['status' => 'hidden']);
                $voting->removeVotes($content);
                if ($content instanceof Question) {
                    foreach ($content->answers()->with('author')->get() as $answer) {
                        $voting->removeVotes($answer);
                    }
                    if ($content->acceptedAnswer) {
                        $reputation->setEvent($content->acceptedAnswer->author, 'accepted:answer:'.$content->accepted_answer_id, 'accepted_answer', 0, $request->user(), $content->acceptedAnswer);
                    }
                }
                if ($content instanceof Answer && $content->question->accepted_answer_id === $content->id) {
                    $content->question->update(['accepted_answer_id' => null]);
                    $reputation->setEvent($content->author, 'accepted:answer:'.$content->id, 'accepted_answer', 0, $request->user(), $content);
                }
            }
            $report->update(['status' => $decision === 'hide' ? 'actioned' : 'dismissed', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            $content->moderationLogs()->create([
                'moderator_id' => $request->user()->id,
                'action' => 'report_'.$decision,
                'note' => $data['note'] ?? null,
            ]);
        });
        if ($decision === 'hide') {
            $content = $report->reportable;
            $url = $content instanceof Question ? route('questions.show', $content) : route('questions.show', $content->question);
            $content->author->notify(new PlatformNotification(__('messages.content_hidden'), $url, 'moderation'));
        }
        $report->reporter->notify(new PlatformNotification(__('messages.report_reviewed'), route('notifications.index'), 'moderation'));

        return back()->with('success', __('messages.moderation_saved'));
    }

    public function history(): View
    {
        return view('moderation.history', [
            'logs' => ModerationLog::with(['moderator', 'moderatable'])->latest()->paginate(25),
        ]);
    }
}
