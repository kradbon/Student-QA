@extends('layouts.app')
@section('title', __('messages.moderation_queue'))
@section('content')
<div class="max-w-5xl">
    <p class="eyebrow">{{ __('messages.moderation') }}</p>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">{{ __('messages.moderation_queue') }}</h1>
    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.pending_are_private') }}</p>

    <section id="questions" class="mt-7" aria-labelledby="pending-heading">
        <div class="flex items-center justify-between gap-3"><h2 id="pending-heading" class="text-lg font-semibold text-slate-950">{{ __('messages.pending_questions') }}</h2><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-900">{{ $questions->total() }}</span></div>
        <div class="mt-3 space-y-3">
            @forelse($questions as $question)
                <article class="surface p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-900">{{ __('messages.status_pending') }}</span><span class="text-xs text-slate-500">{{ $question->category->name }} · {{ $question->created_at->diffForHumans() }}</span></div>
                            <a class="mt-3 block text-lg font-semibold text-slate-950 hover:text-orange-800" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ Str::limit($question->body, 500) }}</p>
                            <p class="mt-3 text-xs text-slate-600">{{ __('messages.by') }} <strong class="text-slate-800">{{ $question->author->name }}</strong></p>
                        </div>
                        <div class="w-full max-w-xs space-y-2">
                            @foreach(['approve'=>__('messages.approve'),'reject'=>__('messages.reject')] as $decision=>$label)
                                <form method="post" action="{{ route('moderator.questions.review', [$question, $decision]) }}" class="space-y-2">@csrf
                                    @if($loop->first)<input type="hidden" name="note" value="">@else<label class="sr-only" for="reject-note-{{ $question->id }}">{{ __('messages.action_note_optional') }}</label><input id="reject-note-{{ $question->id }}" name="note" maxlength="2000" placeholder="{{ __('messages.action_note_optional') }}" class="field w-full">@endif
                                    <button class="{{ $decision === 'approve' ? 'btn-primary' : 'btn-secondary' }} w-full" type="submit">{{ $label }}</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </article>
            @empty<div class="surface p-6 text-sm text-slate-600">{{ __('messages.no_pending_questions') }}</div>@endforelse
        </div>
        <div class="mt-4">{{ $questions->links() }}</div>
    </section>

    <section id="reports" class="mt-9" aria-labelledby="reports-heading">
        <div class="flex items-center justify-between gap-3"><h2 id="reports-heading" class="text-lg font-semibold text-slate-950">{{ __('messages.reported_content') }}</h2><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $reports->total() }}</span></div>
        <div class="mt-3 space-y-3">
            @forelse($reports as $report)
                @php($reported = $report->reportable)
                @if($reported)
                    <article class="surface p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-orange-800">{{ __('messages.report_reason') }}: {{ __('messages.reason_'.$report->reason) }} · {{ __('messages.by') }} {{ $report->reporter->name }}</p>
                                <a class="mt-2 block font-semibold text-slate-950 hover:text-orange-800" href="{{ route('questions.show', $reported instanceof \App\Models\Question ? $reported : $reported->question) }}">{{ $reported->title ?? Str::limit($reported->body, 150) }}</a>
                                @if($report->details)<p class="mt-2 text-sm leading-6 text-slate-600">{{ $report->details }}</p>@endif
                            </div>
                            <div class="w-full max-w-xs space-y-2">
                                @foreach(['dismiss'=>__('messages.dismiss'),'hide'=>__('messages.hide_content')] as $decision=>$label)
                                    <form method="post" action="{{ route('moderator.reports.review', [$report, $decision]) }}" class="space-y-2">@csrf
                                        <label class="sr-only" for="report-note-{{ $report->id }}-{{ $decision }}">{{ __('messages.action_note_optional') }}</label><input id="report-note-{{ $report->id }}-{{ $decision }}" name="note" maxlength="2000" placeholder="{{ __('messages.action_note_optional') }}" class="field w-full">
                                        <button class="{{ $decision === 'hide' ? 'btn-secondary' : 'btn-secondary' }} w-full" type="submit">{{ $label }}</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endif
            @empty<div class="surface p-6 text-sm text-slate-600">{{ __('messages.no_reports') }}</div>@endforelse
        </div>
        <div class="mt-4">{{ $reports->links() }}</div>
    </section>
</div>
@endsection
