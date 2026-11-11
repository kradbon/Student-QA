@extends('layouts.app')
@section('title', __('messages.app_name'))
@section('content')
<section class="grid items-center gap-12 py-10 sm:py-16 lg:grid-cols-[1.05fr_.95fr] lg:gap-16">
    <div class="max-w-2xl">
        <p class="eyebrow">{{ __('messages.landing_eyebrow') }}</p>
        <h1 class="mt-4 text-4xl font-semibold leading-tight tracking-[-.035em] text-slate-950 sm:text-5xl">{{ __('messages.landing_title') }}</h1>
        <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">{{ __('messages.landing_intro') }}</p>
        <div class="mt-8 flex flex-wrap gap-3"><a class="btn-primary px-5" href="{{ route('register') }}">{{ __('messages.landing_primary_cta') }}</a><a class="btn-secondary px-5" href="{{ route('questions.index') }}">{{ __('messages.landing_secondary_cta') }} <span aria-hidden="true">→</span></a></div>
        <p class="mt-4 text-sm text-slate-500">{{ __('messages.landing_signin_prompt') }} <a class="font-semibold text-orange-800 hover:underline" href="{{ route('login') }}">{{ __('messages.login') }}</a></p>
    </div>
    <div class="surface overflow-hidden p-5 sm:p-7">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4"><div><p class="text-sm font-semibold text-slate-900">{{ __('messages.landing_preview_title') }}</p><p class="mt-1 text-xs text-slate-500">{{ __('messages.landing_preview_subtitle') }}</p></div><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-800">{{ __('messages.status_published') }}</span></div>
        <div class="py-5"><p class="text-xs font-semibold uppercase tracking-wide text-orange-800">{{ __('messages.category') }} · {{ __('messages.landing_preview_category') }}</p><h2 class="mt-2 text-xl font-semibold leading-7 text-slate-950">{{ __('messages.landing_preview_question') }}</h2><p class="mt-3 text-sm leading-6 text-slate-600">{{ __('messages.landing_preview_body') }}</p></div>
        <div class="rounded-lg border border-orange-200 bg-orange-50/60 p-4"><div class="flex items-center gap-2 text-sm font-semibold text-orange-900"><span aria-hidden="true">✓</span>{{ __('messages.accepted_answer') }}</div><p class="mt-2 text-sm leading-6 text-slate-700">{{ __('messages.landing_preview_answer') }}</p><p class="mt-3 text-xs font-medium text-slate-500">{{ __('messages.landing_preview_author') }}</p></div>
    </div>
</section>
<section class="border-t border-slate-200 py-10 sm:py-14" aria-label="{{ __('messages.landing_features') }}">
    <div class="grid gap-8 md:grid-cols-3">
        <article><span class="grid size-9 place-items-center rounded-lg bg-orange-50 font-semibold text-orange-800" aria-hidden="true">01</span><h2 class="mt-4 text-base font-semibold text-slate-950">{{ __('messages.landing_feature_questions') }}</h2><p class="mt-2 text-sm leading-6 text-slate-600">{{ __('messages.landing_feature_questions_text') }}</p></article>
        <article><span class="grid size-9 place-items-center rounded-lg bg-orange-50 font-semibold text-orange-800" aria-hidden="true">02</span><h2 class="mt-4 text-base font-semibold text-slate-950">{{ __('messages.landing_feature_answers') }}</h2><p class="mt-2 text-sm leading-6 text-slate-600">{{ __('messages.landing_feature_answers_text') }}</p></article>
        <article><span class="grid size-9 place-items-center rounded-lg bg-orange-50 font-semibold text-orange-800" aria-hidden="true">03</span><h2 class="mt-4 text-base font-semibold text-slate-950">{{ __('messages.landing_feature_trust') }}</h2><p class="mt-2 text-sm leading-6 text-slate-600">{{ __('messages.landing_feature_trust_text') }}</p></article>
    </div>
</section>
<section class="surface flex flex-wrap items-center justify-between gap-5 p-6 sm:p-8"><h2 class="max-w-2xl text-xl font-semibold tracking-tight text-slate-950">{{ __('messages.landing_bottom_title') }}</h2><a class="btn-primary" href="{{ route('register') }}">{{ __('messages.landing_primary_cta') }}</a></section>
@endsection
