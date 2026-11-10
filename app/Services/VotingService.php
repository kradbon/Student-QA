<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VotingService
{
    public function __construct(private readonly ReputationService $reputation) {}

    public function toggle(User $actor, Model $target, int $value): void
    {
        if (! in_array($value, [-1, 1], true)) {
            throw ValidationException::withMessages(['value' => __('messages.invalid_vote')]);
        }

        $owner = $target instanceof Answer ? $target->author : ($target instanceof Question ? $target->author : null);
        if (! $owner || $owner->is($actor)) {
            throw ValidationException::withMessages(['vote' => __('messages.own_vote')]);
        }

        DB::transaction(function () use ($actor, $target, $value, $owner): void {
            $vote = $target->votes()->where('user_id', $actor->id)->lockForUpdate()->first();
            $oldValue = (int) ($vote?->value ?? 0);
            $newValue = $oldValue === $value ? 0 : $value;

            if ($newValue === 0) {
                $vote?->delete();
            } elseif ($vote) {
                $vote->update(['value' => $newValue]);
            } else {
                $target->votes()->create(['user_id' => $actor->id, 'value' => $newValue]);
            }

            $points = $newValue === 1 ? 10 : ($newValue === -1 ? -2 : 0);
            $key = 'vote:'.$target->getMorphClass().':'.$target->getKey().':'.$actor->id.':'.$owner->id;
            $this->reputation->setEvent($owner, $key, 'vote', $points, $actor, $target);

            if ($newValue !== $oldValue) {
                $owner->notify(new PlatformNotification(__('messages.vote_notification'),
                    $target instanceof Answer ? route('questions.show', $target->question_id) : route('questions.show', $target->id),
                    'vote'));
            }
        });
    }

    public function removeVotes(Model $target): void
    {
        DB::transaction(function () use ($target): void {
            foreach ($target->votes()->with('user')->lockForUpdate()->get() as $vote) {
                $owner = $target->author;
                if ($owner) {
                    $key = 'vote:'.$target->getMorphClass().':'.$target->getKey().':'.$vote->user_id.':'.$owner->id;
                    $this->reputation->setEvent($owner, $key, 'vote', 0, $vote->user, $target);
                }
                $vote->delete();
            }
        });
    }
}
