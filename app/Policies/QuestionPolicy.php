<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function view(?User $user, Question $question): bool
    {
        return in_array($question->status, ['published', 'closed'], true)
            || ($user !== null && ($question->user_id === $user->id || $user->isRole('moderator', 'admin')));
    }

    public function create(User $user): bool
    {
        return $user->isRole('student');
    }

    public function update(User $user, Question $question): bool
    {
        return $user->isRole('admin') || ($user->isRole('student') && $question->user_id === $user->id);
    }

    public function delete(User $user, Question $question): bool
    {
        return $user->isRole('admin') || ($user->isRole('student') && $question->user_id === $user->id);
    }

    public function vote(User $user, Question $question): bool
    {
        return $user->isRole('student') && $question->status === 'published' && $question->user_id !== $user->id;
    }

    public function close(User $user, Question $question): bool
    {
        return $question->status === 'published'
            && ($user->isRole('admin') || ($user->isRole('student') && $question->user_id === $user->id));
    }
}
