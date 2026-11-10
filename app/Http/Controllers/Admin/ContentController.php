<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        return view('admin.content.index', [
            'questions' => Question::with(['author', 'category'])->withCount('answers')->latest()->paginate(20, ['*'], 'questions'),
            'answers' => Answer::with(['author', 'question'])->latest()->paginate(20, ['*'], 'answers'),
        ]);
    }
}
