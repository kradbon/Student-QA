<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ModerationLog;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;
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

    public function content(): View
    {
        return view('moderation.content', [
            'questions' => Question::with(['author', 'category'])->withCount('answers')
                ->whereIn('status', ['published', 'closed', 'hidden'])->latest()->paginate(15),
            'answers' => Answer::with(['author', 'question'])->whereIn('status', ['published', 'hidden'])->latest()->paginate(15, ['*'], 'answers'),
        ]);
    }

    public function contentAction(Request $request, Question $question, string $decision, ReputationService $reputation, VotingService $voting): RedirectResponse
    {
        abort_unless(in_array($decision, ['hide', 'restore'], true), 404);
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $question, $decision, $data, $reputation, $voting): void {
            $question = Question::query()->lockForUpdate()->findOrFail($question->id);
            abort_unless(($decision === 'hide' && in_array($question->status, ['published', 'closed'], true))
                || ($decision === 'restore' && $question->status === 'hidden'), 409);
            if ($decision === 'hide') {
                $voting->removeVotes($question);
                foreach ($question->answers()->with(['author', 'votes.user'])->get() as $answer) {
                    $voting->removeVotes($answer);
                }
                if ($question->accepted_answer_id) {
                    $accepted = $question->acceptedAnswer()->with('author')->first();
                    if ($accepted) {
                        $reputation->setEvent($accepted->author, 'accepted:answer:'.$accepted->id, 'accepted_answer', 0, $request->user(), $accepted);
                    }
                    $question->accepted_answer_id = null;
                }
                $question->status = 'hidden';
            } else {
                $question->status = 'published';
            }
            $question->save();
            $question->moderationLogs()->create([
                'moderator_id' => $request->user()->id,
                'action' => 'question_'.$decision,
                'note' => $data['note'] ?? null,
            ]);
        });

        $question->author->notify(new PlatformNotification(
            $decision === 'hide' ? __('messages.content_hidden') : __('messages.content_restored'),
            route('questions.show', $question), 'moderation'));

        return back()->with('success', __('messages.moderation_saved'));
    }

    public function answerAction(Request $request, Answer $answer, string $decision, ReputationService $reputation, VotingService $voting): RedirectResponse
    {
        abort_unless(in_array($decision, ['hide', 'restore'], true), 404);
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $answer, $decision, $data, $reputation, $voting): void {
            $answer = Answer::query()->lockForUpdate()->findOrFail($answer->id);
            abort_unless(($decision === 'hide' && $answer->status === 'published')
                || ($decision === 'restore' && $answer->status === 'hidden'), 409);
            $question = Question::query()->lockForUpdate()->findOrFail($answer->question_id);

            if ($decision === 'hide') {
                $voting->removeVotes($answer);
                if ($question->accepted_answer_id === $answer->id) {
                    $question->update(['accepted_answer_id' => null]);
                    $reputation->setEvent($answer->author, 'accepted:answer:'.$answer->id, 'accepted_answer', 0, $request->user(), $answer);
                }
                $answer->status = 'hidden';
            } else {
                $answer->status = 'published';
            }
            $answer->save();
            $answer->moderationLogs()->create([
                'moderator_id' => $request->user()->id,
                'action' => 'answer_'.$decision,
                'note' => $data['note'] ?? null,
            ]);
        });

        $answer->author->notify(new PlatformNotification(
            $decision === 'hide' ? __('messages.content_hidden') : __('messages.answer_restored'),
            route('questions.show', $answer->question_id), 'moderation'));

        return back()->with('success', __('messages.moderation_saved'));
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
                        $content->update(['accepted_answer_id' => null]);
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

    public function users(Request $request): View
    {
        $users = User::query()
            ->whereKeyNot($request->user()->id)
            ->when(! $request->user()->isRole('admin'), fn ($query) => $query->where('role', 'student'))
            ->when($search = trim((string) $request->query('q')), fn ($query) => $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'suspended' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('moderation.users', ['users' => $users]);
    }

    public function account(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:suspend,ban,restore'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $decision = $data['decision'];
        abort_if($request->user()->is($user), 403);
        abort_if(! $request->user()->isRole('admin') && ! $user->isRole('student'), 403);
        $status = match ($decision) {
            'suspend' => 'suspended',
            'ban' => 'blocked',
            default => 'active',
        };
        abort_if($user->status === $status, 409);
        if ($user->role === 'admin' && $status !== 'active' && User::where('role', 'admin')->where('status', 'active')->count() <= 1) {
            return back()->with('error', __('messages.last_admin'));
        }

        DB::transaction(function () use ($request, $user, $decision, $status, $data): void {
            $user->update(['status' => $status]);
            $user->moderationLogs()->create([
                'moderator_id' => $request->user()->id,
                'action' => 'account_'.$decision,
                'note' => $data['note'] ?? null,
            ]);
        });

        $user->notify(new PlatformNotification(
            __('messages.account_'.$decision.'_notice'),
            route('profile.edit'),
            'account_status',
        ));

        return back()->with('success', __('messages.account_action_saved'));
    }
}
