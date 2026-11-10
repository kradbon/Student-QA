<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Services\PlatformSettingsService;
use App\Services\ReputationService;
use App\Services\SimilarQuestionService;
use App\Services\VotingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Question::query()->with(['author', 'category'])->withCount('answers')
            ->whereIn('status', ['published', 'closed']);

        if ($search = trim((string) $request->query('q', ''))) {
            if (DB::getDriverName() === 'mysql') {
                $query->whereFullText(['title', 'body'], $search);
            } else {
                $query->where(function ($builder) use ($search): void {
                    $builder->where('title', 'like', '%'.$search.'%')->orWhere('body', 'like', '%'.$search.'%');
                });
            }
        }
        if ($category = $request->integer('category')) {
            $query->where('category_id', $category);
        }

        return view('questions.index', [
            'questions' => $query->withCount(['answers' => fn ($builder) => $builder->where('status', 'published')])->latest()->paginate(12)->withQueryString(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function similar(Request $request, SimilarQuestionService $similarity): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:8', 'max:4000'],
            'except' => ['nullable', 'integer', 'exists:questions,id'],
        ]);

        return response()->json(['data' => $similarity->find($data['q'], isset($data['except']) ? (int) $data['except'] : null)]);
    }

    public function create(PlatformSettingsService $settings): View
    {
        $this->authorize('create', Question::class);

        return view('questions.form', [
            'question' => new Question,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'uploadMaxMb' => $settings->get('upload_max_mb', '5'),
        ]);
    }

    public function store(Request $request, PlatformSettingsService $settings): RedirectResponse
    {
        $this->authorize('create', Question::class);
        $uploadMaxKb = max(1024, min(20480, (int) $settings->get('upload_max_mb', '5') * 1024));
        $data = $request->validate([
            'title' => ['required', 'string', 'min:12', 'max:180'],
            'body' => ['required', 'string', 'min:20', 'max:20000'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'attachments' => ['sometimes', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,zip', 'max:'.$uploadMaxKb],
        ]);

        $question = DB::transaction(function () use ($request, $data): Question {
            $question = Question::create([
                'user_id' => $request->user()->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'body' => $data['body'],
                'status' => 'pending',
            ]);
            $this->storeAttachments($request, $question);

            return $question;
        });

        return redirect()->route('questions.show', $question)->with('success', __('messages.question_submitted'));
    }

    public function show(Question $question): View
    {
        $this->authorize('view', $question);
        $question->increment('views');
        $question->load(['author', 'category', 'acceptedAnswer.author', 'attachments', 'answers' => fn ($query) => $query->where('status', 'published')->with(['author', 'attachments', 'votes'])])
            ->loadCount('votes');
        $question->setRelation('answers', $question->answers->sort(function ($first, $second) use ($question): int {
            $acceptedOrder = ($second->id === $question->accepted_answer_id) <=> ($first->id === $question->accepted_answer_id);

            return $acceptedOrder ?: ($second->votes->sum('value') <=> $first->votes->sum('value'));
        }));

        return view('questions.show', ['question' => $question]);
    }

    public function edit(Question $question, PlatformSettingsService $settings): View
    {
        $this->authorize('update', $question);

        return view('questions.form', [
            'question' => $question,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'uploadMaxMb' => $settings->get('upload_max_mb', '5'),
        ]);
    }

    public function update(Request $request, Question $question, PlatformSettingsService $settings): RedirectResponse
    {
        $this->authorize('update', $question);
        $uploadMaxKb = max(1024, min(20480, (int) $settings->get('upload_max_mb', '5') * 1024));
        $data = $request->validate([
            'title' => ['required', 'string', 'min:12', 'max:180'],
            'body' => ['required', 'string', 'min:20', 'max:20000'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'attachments' => ['sometimes', 'array', 'max:'.max(0, 3 - $question->attachments()->count())],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,zip', 'max:'.$uploadMaxKb],
        ]);

        DB::transaction(function () use ($request, $data, $question): void {
            $question->update([...$data, 'status' => 'pending']);
            $this->storeAttachments($request, $question);
        });

        return redirect()->route('questions.show', $question)->with('success', __('messages.question_resubmitted'));
    }

    public function destroy(Question $question, VotingService $voting, ReputationService $reputation): RedirectResponse
    {
        $this->authorize('delete', $question);
        DB::transaction(function () use ($question, $voting, $reputation): void {
            $accepted = $question->acceptedAnswer;
            if ($accepted) {
                $reputation->setEvent($accepted->author, 'accepted:answer:'.$accepted->id, 'accepted_answer', 0, $question->author, $accepted);
            }
            $voting->removeVotes($question);
            foreach ($question->answers()->with('author')->get() as $answer) {
                $voting->removeVotes($answer);
            }
            $question->delete();
        });

        return redirect()->route('student.dashboard')->with('success', __('messages.question_deleted'));
    }

    public function vote(Request $request, Question $question, VotingService $voting): RedirectResponse
    {
        $this->authorize('vote', $question);
        $data = $request->validate(['value' => ['required', 'integer', Rule::in([-1, 1])]]);
        $voting->toggle($request->user(), $question, (int) $data['value']);

        return back()->with('success', __('messages.vote_saved'));
    }

    public function close(Question $question): RedirectResponse
    {
        $this->authorize('close', $question);
        $question->update(['status' => 'closed']);

        return back()->with('success', __('messages.question_closed'));
    }

    private function storeAttachments(Request $request, Question $question): void
    {
        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('question-attachments', 'local');
            $question->attachments()->create([
                'user_id' => $request->user()->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
            ]);
        }
    }
}
