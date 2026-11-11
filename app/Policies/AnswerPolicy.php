<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;

class AnswerPolicy
{
    public function update(User $user, Answer $answer): bool
    {
        return $user->isRole('admin') || ($user->isRole('student') && $answer->user_id === $user->id);
    }

    public function delete(User $user, Answer $answer): bool
    {
        return $user->isRole('admin') || ($user->isRole('student') && $answer->user_id === $user->id);
    }

    public function vote(User $user, Answer $answer): bool
    {
        return $user->isRole('student') && $answer->status === 'published' && $answer->question->status === 'published' && $answer->user_id !== $user->id;
    }

    public function accept(User $user, Answer $answer): bool
    {
        return $answer->status === 'published' && $answer->question->status === 'published'
            && $user->isRole('student') && $answer->question->user_id === $user->id;
    }
}
