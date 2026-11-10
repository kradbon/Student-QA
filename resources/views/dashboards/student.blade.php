@extends('layouts.app')
@section('title', __('messages.dashboard'))
@section('content')
<div class="mb-8 rounded-3xl bg-gradient-to-r from-indigo-700 to-sky-700 p-7 text-white sm:p-9">
    <p class="text-sm font-bold uppercase tracking-widest text-indigo-200">{{ __('messages.student_dashboard') }}</p>
    <h1 class="mt-2 text-3xl font-black">{{ __('messages.welcome') }}, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-indigo-100">{{ __('messages.dashboard_intro') }}</p>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    @foreach([__('messages.reputation') => auth()->user()->reputation, __('messages.my_questions') => $questionCount, __('messages.my_answers') => $answerCount] as $label => $count)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-black text-indigo-700">{{ $count }}</p></div>
    @endforeach
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white p-5"><div class="flex items-center justify-between"><h2 class="text-lg font-bold">{{ __('messages.my_questions') }}</h2><div class="flex gap-3"><a class="text-sm font-semibold text-indigo-700" href="{{ route('student.questions') }}">{{ __('messages.view_all') }}</a><a class="text-sm font-semibold text-indigo-700" href="{{ route('questions.create') }}">{{ __('messages.ask_question') }} +</a></div></div>
        <div class="mt-4 divide-y divide-slate-100">@forelse($questions as $question)<a class="block py-3" href="{{ route('questions.show', $question) }}"><span class="font-semibold">{{ $question->title }}</span><span class="ml-2 rounded-full bg-slate-100 px-2 py-1 text-xs">{{ __('messages.status_'.$question->status) }}</span><span class="mt-1 block text-xs text-slate-500">{{ $question->category->name }} · {{ $question->created_at->diffForHumans() }}</span></a>@empty<p class="py-6 text-sm text-slate-500">{{ __('messages.no_my_questions') }}</p>@endforelse</div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5"><div class="flex items-center justify-between"><h2 class="text-lg font-bold">{{ __('messages.my_answers') }}</h2><a class="text-sm font-semibold text-indigo-700" href="{{ route('student.answers') }}">{{ __('messages.view_all') }}</a></div><div class="mt-4 divide-y divide-slate-100">@forelse($answers as $answer)<a class="block py-3" href="{{ route('questions.show', $answer->question) }}#answer-{{ $answer->id }}"><span class="font-semibold">{{ Str::limit($answer->body, 100) }}</span><span class="mt-1 block text-xs text-slate-500">{{ __('messages.question') }}: {{ $answer->question->title }} · {{ $answer->created_at->diffForHumans() }}</span></a>@empty<p class="py-6 text-sm text-slate-500">{{ __('messages.no_my_answers') }}</p>@endforelse</div></section>
</div>
<section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5"><div class="flex items-center justify-between"><h2 class="text-lg font-bold">{{ __('messages.notifications') }}</h2><a class="text-sm font-semibold text-indigo-700" href="{{ route('notifications.index') }}">{{ __('messages.view_all') }} →</a></div>
    <div class="mt-3 divide-y divide-slate-100">@forelse($notifications as $notification)<a href="{{ $notification->data['url'] ?? route('notifications.index') }}" class="block py-3 text-sm {{ $notification->read_at ? 'text-slate-500' : 'font-semibold text-slate-800' }}">{{ $notification->data['message'] ?? __('messages.notification') }}<span class="ml-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span></a>@empty<p class="py-5 text-sm text-slate-500">{{ __('messages.no_notifications') }}</p>@endforelse</div>
</section>
<section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5"><h2 class="text-lg font-bold">{{ __('messages.reputation_activity') }}</h2><div class="mt-3 divide-y divide-slate-100">@forelse($reputationActivity as $entry)<div class="flex justify-between gap-3 py-3 text-sm"><span>{{ __('messages.reason_'.$entry->reason) }}</span><span class="font-bold {{ $entry->points >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}</span></div>@empty<p class="py-5 text-sm text-slate-500">{{ __('messages.no_reputation_activity') }}</p>@endforelse</div></section>
@endsection
