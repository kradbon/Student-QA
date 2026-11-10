<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Notifications\PlatformNotification;
use App\Services\PlatformSettingsService;
use App\Services\ReputationService;
use App\Services\VotingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnswerController extends Controller
{
    public function store(Request $request, Question $question, PlatformSettingsService $settings): RedirectResponse
    {
        abort_unless($request->user()->isRole('student') && $question->status === 'published', 403);
        $uploadMaxKb = max(1024, min(20480, (int) $settings->get('upload_max_mb', '5') * 1024));
        $data = $request->validate([
            'body' => ['required', 'string', 'min:10', 'max:20000'],
            'attachments' => ['sometimes', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,zip', 'max:'.$uploadMaxKb],
        ]);

        $answer = DB::transaction(function () use ($request, $question, $data): Answer {
            $answer = $question->answers()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
            foreach ($request->file('attachments', []) as $file) {
                $answer->attachments()->create([
                    'user_id' => $request->user()->id,
                    'disk' => 'local',
                    'path' => $file->store('answer-attachments', 'local'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'size' => $file->getSize(),
                ]);
            }

            return $answer;
        });

        if ($question->user_id !== $request->user()->id) {
            $question->author->notify(new PlatformNotification(__('messages.answer_notification'), route('questions.show', $question), 'answer'));
        }

        return redirect()->route('questions.show', $question)->with('success', __('messages.answer_submitted'))->withFragment('answer-'.$answer->id);
    }

    public function update(Request $request, Answer $answer): RedirectResponse
    {
        $this->authorize('update', $answer);
        $data = $request->validate(['body' => ['required', 'string', 'min:10', 'max:20000']]);
        $answer->update($data);

        return back()->with('success', __('messages.answer_updated'));
    }

    public function destroy(Answer $answer, VotingService $voting, ReputationService $reputation): RedirectResponse
    {
        $this->authorize('delete', $answer);
        $question = $answer->question;
        DB::transaction(function () use ($answer, $question, $voting, $reputation): void {
            if ($question->accepted_answer_id === $answer->id) {
                $question->update(['accepted_answer_id' => null]);
                $reputation->setEvent($answer->author, 'accepted:answer:'.$answer->id, 'accepted_answer', 0, $question->author, $answer);
            }
            $voting->removeVotes($answer);
            $answer->delete();
        });

        return redirect()->route('questions.show', $question)->with('success', __('messages.answer_deleted'));
    }

    public function accept(Request $request, Answer $answer, ReputationService $reputation): RedirectResponse
    {
        $this->authorize('accept', $answer);
        $question = $answer->question;
        DB::transaction(function () use ($answer, $question, $request, $reputation): void {
            $question->newQuery()->whereKey($question->id)->lockForUpdate()->firstOrFail();
            $previous = $question->acceptedAnswer;
            if ($previous?->is($answer)) {
                return;
            }
            if ($previous) {
                $reputation->setEvent($previous->author, 'accepted:answer:'.$previous->id, 'accepted_answer', 0, $request->user(), $previous);
            }
            $question->update(['accepted_answer_id' => $answer->id]);
            $reputation->setEvent($answer->author, 'accepted:answer:'.$answer->id, 'accepted_answer', 15, $request->user(), $answer);
        });

        $answer->author->notify(new PlatformNotification(__('messages.accepted_notification'), route('questions.show', $question), 'accepted_answer'));

        return back()->with('success', __('messages.answer_accepted'));
    }

    public function vote(Request $request, Answer $answer, VotingService $voting): RedirectResponse
    {
        $this->authorize('vote', $answer);
        $data = $request->validate(['value' => ['required', 'integer', Rule::in([-1, 1])]]);
        $voting->toggle($request->user(), $answer, (int) $data['value']);

        return back()->with('success', __('messages.vote_saved'));
    }
}
