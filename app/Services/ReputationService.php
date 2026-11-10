<?php

namespace App\Services;

use App\Models\ReputationTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReputationService
{
    public function setEvent(User $recipient, string $eventKey, string $reason, int $points, ?User $actor = null, ?Model $source = null): void
    {
        DB::transaction(function () use ($recipient, $eventKey, $reason, $points, $actor, $source): void {
            $transaction = ReputationTransaction::query()->where('event_key', $eventKey)->lockForUpdate()->first();
            $oldPoints = (int) ($transaction?->points ?? 0);

            if ($transaction) {
                $transaction->update(['points' => $points, 'reason' => $reason, 'actor_id' => $actor?->id]);
                if ($source) {
                    $transaction->source()->associate($source);
                    $transaction->save();
                }
            } elseif ($points !== 0) {
                $transaction = new ReputationTransaction([
                    'user_id' => $recipient->id,
                    'actor_id' => $actor?->id,
                    'event_key' => $eventKey,
                    'reason' => $reason,
                    'points' => $points,
                ]);
                if ($source) {
                    $transaction->source()->associate($source);
                }
                $transaction->save();
            }

            $delta = $points - $oldPoints;
            if ($delta !== 0) {
                $recipient->newQuery()->whereKey($recipient->id)->increment('reputation', $delta);
                $recipient->refresh();
            }
        });
    }
}
