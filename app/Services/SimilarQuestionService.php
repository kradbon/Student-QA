<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Str;

class SimilarQuestionService
{
    public function find(string $text, ?int $exceptId = null): array
    {
        $text = Str::lower(trim($text));
        $terms = collect(preg_split('/[^\pL\pN]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [])
            ->filter(fn (string $term) => mb_strlen($term) >= 3)
            ->unique()->take(12)->values();

        if ($terms->isEmpty() || mb_strlen($text) < 8) {
            return [];
        }

        $query = Question::query()->whereIn('status', ['published', 'closed'])
            ->when($exceptId, fn ($builder) => $builder->where('id', '<>', $exceptId));
        if (Question::query()->getConnection()->getDriverName() === 'mysql') {
            $query->whereFullText(['title', 'body'], $text);
        } else {
            $query->where(function ($builder) use ($terms): void {
                foreach ($terms as $term) {
                    $builder->orWhere('title', 'like', '%'.$term.'%')->orWhere('body', 'like', '%'.$term.'%');
                }
            });
        }
        $candidates = $query->latest()->limit(100)->get(['id', 'title', 'body']);

        return $candidates->map(function (Question $question) use ($terms, $text): Question {
            $candidateText = Str::lower($question->title.' '.$question->body);
            $candidateTerms = collect(preg_split('/[^\pL\pN]+/u', $candidateText, -1, PREG_SPLIT_NO_EMPTY) ?: [])
                ->filter(fn (string $term) => mb_strlen($term) >= 3)->unique();
            $intersection = $terms->intersect($candidateTerms)->count();
            $union = max(1, $terms->count() + $candidateTerms->count() - $intersection);
            similar_text(Str::lower($question->title), Str::limit($text, 180), $titleSimilarity);
            $question->similarity = round((($intersection / $union) * 75) + ($titleSimilarity * 0.25), 1);

            return $question;
        })->filter(fn (Question $question) => $question->similarity >= 12)
            ->sortByDesc('similarity')->take(5)
            ->map(fn (Question $question) => [
                'id' => $question->id,
                'title' => $question->title,
                'similarity' => $question->similarity,
                'url' => route('questions.show', $question),
            ])->values()->all();
    }
}
