<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            $dashboard = match (auth()->user()->role) {
                'admin' => 'admin.dashboard',
                'moderator' => 'moderator.dashboard',
                default => 'student.dashboard',
            };

            return redirect()->route($dashboard);
        }

        return view('welcome', [
            'communityStats' => [
                'students' => User::query()->where('role', 'student')->where('status', 'active')->count(),
                'questions' => Question::query()->whereIn('status', ['published', 'closed'])->count(),
                'answers' => Answer::query()->where('status', 'published')->count(),
            ],
            'topStudents' => User::query()->where('role', 'student')->where('status', 'active')
                ->orderByDesc('reputation')->orderBy('name')->orderBy('id')->limit(3)->get(),
        ]);
    }
}
