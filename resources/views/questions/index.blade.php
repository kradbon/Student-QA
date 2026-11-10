@extends('layouts.app')
@section('title', __('messages.questions'))
@section('content')
<section class="mb-8 rounded-3xl bg-gradient-to-br from-indigo-700 to-sky-700 px-6 py-10 text-white sm:px-10">
    <p class="text-sm font-bold uppercase tracking-widest text-indigo-200">{{ __('messages.community') }}</p>
    <h1 class="mt-2 text-3xl font-black sm:text-4xl">{{ __('messages.find_answers') }}</h1>
    <p class="mt-3 max-w-2xl text-indigo-100">{{ __('messages.home_intro') }}</p>
    <form method="get" action="{{ route('home') }}" class="mt-6 grid gap-3 sm:grid-cols-[1fr_220px_auto]">
        <input name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}" class="rounded-xl border-0 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-indigo-300">
        <select name="category" class="rounded-xl border-0 px-4 py-3 text-slate-900">
            <option value="">{{ __('messages.all_categories') }}</option>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach
        </select>
        <button class="rounded-xl bg-white px-5 py-3 font-bold text-indigo-700 hover:bg-indigo-50">{{ __('messages.search') }}</button>
    </form>
</section>
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-xl font-bold">{{ __('messages.latest_questions') }}</h2>
    @auth @if(auth()->user()->isRole('student'))<a class="font-semibold text-indigo-700" href="{{ route('questions.create') }}">{{ __('messages.ask_question') }} →</a>@endif @endauth
</div>
<div class="space-y-3">
@forelse($questions as $question)
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300">
        <div class="flex gap-4">
            <div class="hidden min-w-16 text-center sm:block"><strong class="block text-xl text-indigo-700">{{ $question->answers_count }}</strong><span class="text-xs text-slate-500">{{ __('messages.answers') }}</span></div>
            <div class="min-w-0 flex-1">
                <a class="text-lg font-bold text-slate-900 hover:text-indigo-700" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ Str::limit($question->body, 220) }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500"><span class="rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700">{{ $question->category->name }}</span>@if($question->status === 'closed')<span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold">{{ __('messages.status_closed') }}</span>@endif<span>{{ __('messages.by') }} {{ $question->author->name }}</span><span>{{ $question->created_at->diffForHumans() }}</span><span>{{ $question->views }} {{ __('messages.views') }}</span></div>
            </div>
        </div>
    </article>
@empty
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><h3 class="text-lg font-bold">{{ __('messages.no_questions') }}</h3><p class="mt-2 text-slate-500">{{ __('messages.no_questions_help') }}</p></div>
@endforelse
</div>
<div class="mt-6">{{ $questions->links() }}</div>
@endsection
