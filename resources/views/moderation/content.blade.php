@extends('layouts.app')
@section('title', __('messages.review_content'))
@section('content')
<div class="max-w-5xl">
    <p class="eyebrow">{{ __('messages.moderation') }}</p>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">{{ __('messages.review_content') }}</h1>
    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.content_review_help') }}</p>
    <div class="mt-6 space-y-4">
        @forelse($questions as $question)
            <article class="surface p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $question->status === 'hidden' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-50 text-emerald-800' }}">{{ __('messages.status_'.$question->status) }}</span>
                            <span class="text-xs text-slate-500">{{ $question->category->name }} · {{ $question->created_at->diffForHumans() }}</span>
                        </div>
                        <a class="mt-3 block text-lg font-semibold text-slate-950 hover:text-orange-800" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ Str::limit($question->body, 700) }}</p>
                        <p class="mt-3 text-xs text-slate-500">{{ $question->author->name }} · {{ $question->answers_count }} {{ __('messages.answers') }}</p>
                    </div>
                    <form method="post" action="{{ route('moderator.content.action', [$question, $question->status === 'hidden' ? 'restore' : 'hide']) }}" class="w-full max-w-sm space-y-2">
                        @csrf
                        <label class="sr-only" for="note-{{ $question->id }}">{{ __('messages.action_note_optional') }}</label>
                        <input id="note-{{ $question->id }}" name="note" maxlength="2000" placeholder="{{ __('messages.action_note_optional') }}" class="field w-full">
                        <button class="{{ $question->status === 'hidden' ? 'btn-primary' : 'btn-secondary' }} w-full" type="submit">{{ $question->status === 'hidden' ? __('messages.restore_content') : __('messages.hide_content') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="surface p-8 text-center text-sm text-slate-600">{{ __('messages.no_moderated_content') }}</div>
        @endforelse
    </div>
    <div class="mt-5">{{ $questions->links() }}</div>

    <section class="mt-9" aria-labelledby="answers-review-heading">
        <div class="flex items-center justify-between gap-3"><h2 id="answers-review-heading" class="text-lg font-semibold text-slate-950">{{ __('messages.answers') }}</h2><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $answers->total() }}</span></div>
        <div class="mt-3 space-y-3">
            @forelse($answers as $answer)
                <article class="surface p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $answer->status === 'hidden' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-50 text-emerald-800' }}">{{ __('messages.status_'.$answer->status) }}</span><span class="text-xs text-slate-500">{{ $answer->author->name }} · {{ $answer->created_at->diffForHumans() }}</span></div>
                            <a class="mt-3 block font-semibold text-slate-950 hover:text-orange-800" href="{{ route('questions.show', $answer->question) }}">{{ $answer->question->title }}</a>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ Str::limit($answer->body, 600) }}</p>
                        </div>
                        <form method="post" action="{{ route('moderator.answers.action', [$answer, $answer->status === 'hidden' ? 'restore' : 'hide']) }}" class="w-full max-w-sm space-y-2">@csrf
                            <label class="sr-only" for="answer-note-{{ $answer->id }}">{{ __('messages.action_note_optional') }}</label><input id="answer-note-{{ $answer->id }}" name="note" maxlength="2000" placeholder="{{ __('messages.action_note_optional') }}" class="field w-full">
                            <button class="{{ $answer->status === 'hidden' ? 'btn-primary' : 'btn-secondary' }} w-full" type="submit">{{ $answer->status === 'hidden' ? __('messages.restore_answer') : __('messages.hide_content') }}</button>
                        </form>
                    </div>
                </article>
            @empty<div class="surface p-6 text-sm text-slate-600">{{ __('messages.no_moderated_answers') }}</div>@endforelse
        </div>
        <div class="mt-4">{{ $answers->links() }}</div>
    </section>
</div>
@endsection
