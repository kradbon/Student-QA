@extends('layouts.app')
@section('title', __('messages.questions'))
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div><p class="eyebrow">{{ __('messages.community') }}</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ __('messages.find_answers') }}</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.home_intro') }}</p></div>
    @auth @if(auth()->user()->isRole('student'))<a class="btn-primary" href="{{ route('questions.create') }}">{{ __('messages.ask_question') }} <span aria-hidden="true">+</span></a>@endif @endauth
</div>

<form method="get" action="{{ route('questions.index') }}" class="surface mb-6 grid gap-3 p-4 md:grid-cols-[minmax(12rem,1fr)_13rem_12rem_auto]">
    <label class="block text-sm font-semibold text-slate-700">{{ __('messages.search') }}<input name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}" class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5 text-sm placeholder:text-slate-500"></label>
    <label class="block text-sm font-semibold text-slate-700">{{ __('messages.category') }}<select name="category" class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5 text-sm"><option value="">{{ __('messages.all_categories') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <label class="block text-sm font-semibold text-slate-700">{{ __('messages.sort_questions') }}<select name="sort" class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5 text-sm"><option value="latest" @selected($sort === 'latest')>{{ __('messages.sort_latest') }}</option><option value="popular" @selected($sort === 'popular')>{{ __('messages.sort_popular') }}</option><option value="unanswered" @selected($sort === 'unanswered')>{{ __('messages.sort_unanswered') }}</option></select></label>
    <button class="btn-primary self-end" type="submit">{{ __('messages.search') }}</button>
</form>

<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_16rem]">
    <section aria-labelledby="question-list-heading">
        <div class="mb-3 flex items-center justify-between"><h2 id="question-list-heading" class="text-lg font-bold text-slate-900">{{ __('messages.latest_questions') }}</h2><span class="text-sm text-slate-500">{{ $questions->total() }}</span></div>
        <div class="surface divide-y divide-slate-200 overflow-hidden">
        @forelse($questions as $question)
            <article class="flex gap-4 p-5 transition-colors hover:bg-slate-50 sm:gap-5">
                <div class="hidden w-16 shrink-0 text-right sm:block"><p class="text-lg font-semibold tabular-nums text-slate-800">{{ $question->answers_count }}</p><p class="text-xs text-slate-500">{{ __('messages.answers') }}</p><p class="mt-2 text-sm font-semibold tabular-nums text-slate-700">{{ $question->votes_count }}</p><p class="text-xs text-slate-500">{{ __('messages.votes') }}</p></div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2"><a class="text-base font-semibold leading-6 text-blue-800 hover:text-blue-950 hover:underline" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>@if($question->accepted_answer_id)<span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800">{{ __('messages.accepted_answer') }}</span>@elseif($question->status === 'closed')<span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ __('messages.status_closed') }}</span>@endif</div>
                    <p class="mt-1.5 line-clamp-2 text-sm leading-6 text-slate-600">{{ Str::limit($question->body, 240) }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-600"><a class="rounded-md bg-blue-50 px-2 py-1 font-semibold text-blue-800 hover:bg-blue-100" href="{{ route('home', ['category' => $question->category_id]) }}">{{ $question->category->name }}</a><span>{{ __('messages.by') }} <strong class="font-semibold text-slate-800">{{ $question->author->name }}</strong></span><time datetime="{{ $question->created_at->toIso8601String() }}">{{ $question->created_at->diffForHumans() }}</time><span>{{ $question->views }} {{ __('messages.views') }}</span><span class="sm:hidden">{{ $question->answers_count }} {{ __('messages.answers') }} · {{ $question->votes_count }} {{ __('messages.votes') }}</span></div>
                </div>
            </article>
        @empty
            <div class="p-10 text-center"><h3 class="font-semibold text-slate-900">{{ __('messages.no_questions') }}</h3><p class="mt-1 text-sm text-slate-600">{{ __('messages.no_questions_help') }}</p></div>
        @endforelse
        </div>
        <div class="mt-5">{{ $questions->links() }}</div>
    </section>

    <aside class="surface p-5" aria-labelledby="categories-heading"><h2 id="categories-heading" class="text-base font-bold text-slate-900">{{ __('messages.category') }}</h2><ul class="mt-3 space-y-1">@foreach($categories as $category)<li><a class="flex min-h-10 items-center justify-between rounded-lg px-2.5 text-sm text-slate-700 hover:bg-slate-100 hover:text-blue-800" href="{{ route('questions.index', ['category' => $category->id]) }}"><span>{{ $category->name }}</span><span aria-hidden="true">→</span></a></li>@endforeach</ul><a class="mt-4 inline-flex text-sm font-semibold text-blue-800 hover:underline" href="{{ route('questions.index') }}">{{ __('messages.all_categories') }}</a></aside>
</div>
@endsection
