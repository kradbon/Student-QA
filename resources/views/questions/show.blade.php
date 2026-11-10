@extends('layouts.app')
@section('title', $question->title)
@section('content')
<div class="mx-auto max-w-4xl">
    @if($question->status !== 'published')<div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">{{ __('messages.question_status') }}: <strong>{{ __('messages.status_'.$question->status) }}</strong></div>@endif
    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500"><span class="rounded-full bg-indigo-50 px-3 py-1 font-semibold text-indigo-700">{{ $question->category->name }}</span><span>{{ __('messages.by') }} {{ $question->author->name }}</span><span>{{ $question->created_at->diffForHumans() }}</span><span>{{ $question->votes_count }} {{ __('messages.votes') }}</span><span>{{ $question->views }} {{ __('messages.views') }}</span></div>
        <h1 class="mt-4 text-3xl font-black leading-tight">{{ $question->title }}</h1>
        <div class="prose prose-slate mt-5 max-w-none whitespace-pre-line leading-7">{{ $question->body }}</div>
        @if($question->attachments->isNotEmpty())<div class="mt-5 border-t border-slate-100 pt-4"><h2 class="text-sm font-bold">{{ __('messages.attachments') }}</h2>@foreach($question->attachments as $file)<a class="mt-2 mr-3 inline-block text-sm font-semibold text-indigo-700" href="{{ route('attachments.question', $file) }}">{{ $file->original_name }}</a>@endforeach</div>@endif
        <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
            @auth @if($question->status === 'published' && $question->user_id !== auth()->id())
                @foreach([1 => __('messages.upvote'), -1 => __('messages.downvote')] as $value => $label)<form method="post" action="{{ route('questions.vote', $question) }}">@csrf<input type="hidden" name="value" value="{{ $value }}"><button class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold hover:border-indigo-400">{{ $label }}</button></form>@endforeach
            @endif @endauth
            @can('update', $question)<a class="ml-auto text-sm font-semibold text-indigo-700" href="{{ route('questions.edit', $question) }}">{{ __('messages.edit') }}</a>@endcan
            @can('close', $question)<form method="post" action="{{ route('questions.close', $question) }}">@csrf @method('PATCH')<button class="text-sm font-semibold text-slate-600">{{ __('messages.close_question') }}</button></form>@endcan
            @can('delete', $question)<form method="post" action="{{ route('questions.destroy', $question) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">{{ __('messages.delete') }}</button></form>@endcan
            @auth @if($question->status === 'published')<form method="post" action="{{ route('reports.store') }}" class="ml-auto">@csrf<input type="hidden" name="type" value="question"><input type="hidden" name="id" value="{{ $question->id }}"><input type="hidden" name="reason" value="inappropriate"><button class="text-sm text-slate-500 hover:text-rose-700">{{ __('messages.report') }}</button></form>@endif @endauth
        </div>
    </article>

    <div class="mb-4 mt-9 flex items-center justify-between"><h2 class="text-2xl font-black">{{ __('messages.answers') }} ({{ $question->answers->count() }})</h2></div>
    <div class="space-y-4">
        @forelse($question->answers as $answer)
            <article id="answer-{{ $answer->id }}" class="rounded-2xl border {{ $question->accepted_answer_id === $answer->id ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-white' }} p-6 shadow-sm">
                @if($question->accepted_answer_id === $answer->id)<p class="mb-3 font-bold text-emerald-700">✓ {{ __('messages.accepted_answer') }}</p>@endif
                <p class="whitespace-pre-line leading-7">{{ $answer->body }}</p>
                @if($answer->attachments->isNotEmpty())<div class="mt-3">@foreach($answer->attachments as $file)<a class="mr-3 text-sm text-indigo-700" href="{{ route('attachments.answer', $file) }}">{{ $file->original_name }}</a>@endforeach</div>@endif
                <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4 text-sm text-slate-500"><span>{{ __('messages.by') }} <strong class="text-slate-700">{{ $answer->author->name }}</strong></span><span>{{ $answer->votes->sum('value') }} {{ __('messages.score') }}</span><span>{{ $answer->created_at->diffForHumans() }}</span>
                    @auth @if($answer->user_id !== auth()->id() && $question->status === 'published')
                        @foreach([1 => __('messages.upvote'), -1 => __('messages.downvote')] as $value => $label)<form method="post" action="{{ route('answers.vote', $answer) }}">@csrf<input type="hidden" name="value" value="{{ $value }}"><button class="font-semibold text-indigo-700">{{ $label }}</button></form>@endforeach
                    @endif @endauth
                    @can('accept', $answer) @if($question->accepted_answer_id !== $answer->id)<form method="post" action="{{ route('answers.accept', $answer) }}">@csrf<button class="font-semibold text-emerald-700">{{ __('messages.accept_answer') }}</button></form>@endif @endcan
                    @can('update', $answer)<details class="basis-full"><summary class="cursor-pointer font-semibold text-indigo-700">{{ __('messages.edit_answer') }}</summary><form method="post" action="{{ route('answers.update', $answer) }}" class="mt-3">@csrf @method('PUT')<textarea name="body" rows="4" required class="w-full rounded-xl border-slate-300">{{ $answer->body }}</textarea><button class="mt-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white">{{ __('messages.save_changes') }}</button></form></details>@endcan
                    @can('delete', $answer)<form method="post" action="{{ route('answers.destroy', $answer) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="font-semibold text-rose-700">{{ __('messages.delete') }}</button></form>@endcan
                    @auth @if($question->status === 'published')<form method="post" action="{{ route('reports.store') }}">@csrf<input type="hidden" name="type" value="answer"><input type="hidden" name="id" value="{{ $answer->id }}"><input type="hidden" name="reason" value="inappropriate"><button class="text-slate-500">{{ __('messages.report') }}</button></form>@endif @endauth
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">{{ __('messages.no_answers') }}</div>
        @endforelse
    </div>

    @auth @if(auth()->user()->isRole('student') && $question->status === 'published')
        <form method="post" enctype="multipart/form-data" action="{{ route('answers.store', $question) }}" class="mt-7 rounded-2xl border border-slate-200 bg-white p-6">@csrf<h2 class="text-xl font-bold">{{ __('messages.your_answer') }}</h2><textarea class="mt-4 w-full rounded-xl border-slate-300" name="body" rows="6" minlength="10" maxlength="20000" required>{{ old('body') }}</textarea><label class="mt-4 block text-sm font-semibold">{{ __('messages.attachments') }}<input class="mt-2 block w-full rounded-xl border border-slate-300 p-2 text-sm" type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx,.zip"></label><button class="mt-4 rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">{{ __('messages.submit_answer') }}</button></form>
    @endif @else<p class="mt-7 rounded-xl bg-indigo-50 p-4 text-indigo-900"><a class="font-bold" href="{{ route('login') }}">{{ __('messages.login') }}</a> {{ __('messages.login_to_answer') }}</p>@endauth
</div>
@endsection
