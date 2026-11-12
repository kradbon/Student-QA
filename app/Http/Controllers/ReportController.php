<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isRole('student'), 403);

        $data = $request->validate([
            'type' => ['required', Rule::in(['question', 'answer'])],
            'id' => ['required', 'integer'],
            'reason' => ['required', Rule::in(['inappropriate', 'spam', 'incorrect', 'other'])],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $reported = $data['type'] === 'question' ? Question::findOrFail($data['id']) : Answer::findOrFail($data['id']);
        abort_unless($reported instanceof Question ? $reported->status === 'published' : $reported->status === 'published', 404);
        abort_unless($reported->user_id !== $request->user()->id, 403);

        $report = $reported->reports()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['reason' => $data['reason'], 'details' => $data['details'] ?? null, 'status' => 'pending'],
        );

        if ($report->wasRecentlyCreated) {
            User::whereIn('role', ['moderator', 'admin'])->where('status', 'active')->each(function (User $moderator): void {
                $moderator->notify(new PlatformNotification(__('messages.report_notification'), route('moderator.queue'), 'report'));
            });
        }

        return back()->with('success', __('messages.report_submitted'));
    }
}
