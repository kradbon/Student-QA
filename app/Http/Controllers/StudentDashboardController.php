<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboards.student', [
            'questions' => $user->questions()->with('category')->latest()->take(5)->get(),
            'answers' => $user->answers()->with('question')->latest()->take(5)->get(),
            'questionCount' => $user->questions()->count(),
            'answerCount' => $user->answers()->count(),
            'notifications' => $user->notifications()->latest()->take(5)->get(),
            'reputationActivity' => $user->reputationTransactions()->latest()->take(8)->get(),
        ]);
    }

    public function questions(Request $request): View
    {
        return view('dashboards.my-questions', [
            'questions' => $request->user()->questions()->with('category')->latest()->paginate(15),
        ]);
    }

    public function answers(Request $request): View
    {
        return view('dashboards.my-answers', [
            'answers' => $request->user()->answers()->with('question')->latest()->paginate(15),
        ]);
    }
}
