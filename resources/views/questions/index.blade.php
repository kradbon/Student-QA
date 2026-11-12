@extends('layouts.app')
@section('title', __('messages.questions'))
@section('content')
<div class="grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_320px]">
    <div class="min-w-0">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div><p class="eyebrow">{{ __('messages.community') }}</p><h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">{{ __('messages.all_questions') }}</h1></div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-wrap rounded-lg border border-slate-300 bg-white p-1 text-sm">
                    @foreach(['latest' => __('messages.sort_latest'), 'popular' => __('messages.sort_popular'), 'unanswered' => __('messages.sort_unanswered')] as $key => $label)
                        <a class="rounded-md px-3 py-1.5 font-medium {{ $sort === $key ? 'bg-slate-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}" href="{{ route('questions.index', array_filter(['q' => request('q'), 'category' => request('category'), 'sort' => $key])) }}">{{ $label }}</a>
                    @endforeach
                </div>
                @auth @if(auth()->user()->isRole('student'))<a class="btn-primary min-h-10 px-3 py-2 text-sm" href="{{ route('student.questions.create') }}"><span aria-hidden="true">+</span>{{ __('messages.ask_question') }}</a>@endif @endauth
            </div>
        </div>

        @if($featuredQuestions->isNotEmpty())
        <section class="mt-5" aria-label="{{ __('messages.latest_questions') }}">
            <div class="mb-3 flex items-center justify-between"><h2 class="text-sm font-semibold text-slate-800">{{ __('messages.featured_questions') }}</h2><span class="text-xs text-slate-500">{{ $questions->total() }} {{ __('messages.questions') }}</span></div>
            <div class="grid gap-3 md:grid-cols-3">
                @foreach($featuredQuestions as $featured)
                    <a href="{{ route('questions.show', $featured) }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                        <p class="line-clamp-2 min-h-12 font-semibold leading-6 text-slate-900 group-hover:text-blue-700">{{ $featured->title }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-slate-500"><span>▲ {{ $featured->votes_count }} {{ __('messages.votes') }}</span>@if($featured->accepted_answer_id)<span class="rounded-full bg-emerald-600 px-2 py-1 font-semibold text-white">✓ {{ $featured->answers_count }} {{ __('messages.answers') }}</span>@else<span>▤ {{ $featured->answers_count }} {{ __('messages.answers') }}</span>@endif</div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        <section class="mt-6 border-t border-slate-200" aria-labelledby="question-list-heading">
            <div class="flex items-center justify-between py-4"><h2 id="question-list-heading" class="text-lg font-semibold text-slate-900">{{ __('messages.latest_questions') }}</h2><span class="text-sm text-slate-500">{{ $questions->total() }}</span></div>
            <div class="divide-y divide-slate-200">
            @forelse($questions as $question)
                <article class="py-5 sm:px-2">
                    <div class="mb-2 flex flex-wrap items-center gap-2 text-xs text-slate-500"><x-user-avatar :user="$question->author" size="sm"/><a class="font-semibold text-blue-700 hover:underline" href="{{ route('questions.show', $question) }}">{{ $question->author->name }}</a><span class="font-medium text-slate-700">{{ number_format($question->author->reputation) }}</span><span aria-hidden="true">·</span><time datetime="{{ $question->created_at->toIso8601String() }}">{{ $question->created_at->diffForHumans() }}</time></div>
                    <a class="text-lg font-semibold leading-7 text-slate-900 hover:text-blue-700" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                    <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-600">{{ Str::limit($question->body, 250) }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2"><a class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700" href="{{ route('questions.index', ['category' => $question->category_id]) }}">{{ $question->category->name }}</a>@if($question->accepted_answer_id)<span class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">✓ {{ __('messages.accepted_answer') }}</span>@endif</div>
                    <div class="mt-3 flex flex-wrap items-center gap-5 text-xs font-medium text-slate-500"><span class="text-slate-800">▲ {{ $question->votes_count }} {{ __('messages.votes') }}</span><span class="{{ $question->accepted_answer_id ? 'text-emerald-700' : '' }}">▤ {{ $question->answers_count }} {{ __('messages.answers') }}</span><span>◒ {{ number_format($question->views) }} {{ __('messages.views') }}</span></div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center"><h3 class="font-semibold text-slate-900">{{ __('messages.no_questions') }}</h3><p class="mt-1 text-sm text-slate-600">{{ __('messages.no_questions_help') }}</p></div>
            @endforelse
            </div>
            <div class="mt-5">{{ $questions->links() }}</div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-[84px]">
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white" aria-labelledby="following-heading"><div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3"><h2 id="following-heading" class="font-semibold text-slate-800">{{ __('messages.following_tags') }}</h2><a class="text-xs font-medium text-blue-700 hover:underline" href="{{ route('community.tags') }}">{{ __('messages.all_categories') }}</a></div><div class="flex flex-wrap gap-2 p-4">@foreach($categories as $category)<a class="rounded-md bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700" href="{{ route('questions.index', ['category' => $category->id]) }}">{{ $category->name }}</a>@endforeach</div></section>
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white" aria-labelledby="hot-heading"><h2 id="hot-heading" class="border-b border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-800">{{ __('messages.sort_popular') }}</h2><div class="divide-y divide-slate-200">@forelse($hotQuestions as $hot)<a class="block px-4 py-3 transition hover:bg-slate-50" href="{{ route('questions.show', $hot) }}"><span class="block text-sm leading-5 text-slate-800 hover:text-blue-700">{{ Str::limit($hot->title, 90) }}</span><span class="mt-2 inline-flex items-center gap-1 text-xs font-medium {{ $hot->accepted_answer_id ? 'text-emerald-700' : 'text-slate-500' }}">{{ $hot->accepted_answer_id ? '✓' : '▤' }} {{ $hot->answers_count }} {{ __('messages.answers') }}</span></a>@empty<p class="px-4 py-5 text-sm text-slate-500">{{ __('messages.no_questions') }}</p>@endforelse</div></section>
    </aside>
</div>
@endsection
