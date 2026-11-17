@extends('layouts.app')
@section('title', __('messages.app_name'))
@section('content')
<div class="landing-page">
    <section class="landing-hero grid items-center gap-10 lg:grid-cols-[.94fr_1.06fr] lg:gap-16" aria-labelledby="landing-title">
        <div class="landing-hero__copy">
            <p class="landing-eyebrow"><span aria-hidden="true"></span>{{ __('messages.landing_badge') }}</p>
            <h1 id="landing-title">{{ __('messages.landing_headline') }}</h1>
            <p class="landing-hero__description">{{ __('messages.landing_description') }}</p>
            <div class="landing-hero__actions">
                <a class="btn-primary" href="{{ route('register') }}">{{ __('messages.landing_primary_cta') }}</a>
                <a class="btn-secondary" href="{{ route('questions.index') }}">{{ __('messages.landing_secondary_cta') }}</a>
            </div>
            <p class="landing-hero__microcopy">{{ __('messages.landing_existing_account') }} <a href="{{ route('login') }}">{{ __('messages.login') }}</a></p>
        </div>

        <div class="landing-product" aria-label="{{ __('messages.landing_questions_heading') }}">
            @if($recentQuestions->isNotEmpty())
                @php($spotlight = $recentQuestions->first())
                <div class="landing-product__sample-heading"><div><h2>{{ __('messages.landing_question_example_title') }}</h2><span>{{ __('messages.landing_question_example') }}</span></div><span class="landing-product__published">{{ __('messages.status_published') }}</span></div>
                <article class="landing-product__question">
                    <div class="landing-product__meta"><span>{{ $spotlight->category?->name ?? __('messages.categories') }}</span><span>{{ $spotlight->created_at->diffForHumans() }}</span></div>
                    <h2><a href="{{ route('questions.show', $spotlight) }}">{{ $spotlight->title }}</a></h2>
                    <p>{{ \Illuminate\Support\Str::limit(trim(strip_tags($spotlight->body)), 180) }}</p>
                    <div class="landing-product__stats"><span><b>{{ (int) $spotlight->votes_sum_value }}</b> {{ __('messages.score') }}</span><span><b>{{ $spotlight->answers_count }}</b> {{ __('messages.answers') }}</span>@if($spotlight->accepted_answer_id)<span class="landing-product__accepted"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ __('messages.accepted_answer') }}</span>@endif</div>
                </article>
                @if($spotlight->acceptedAnswer)
                    <article class="landing-product__answer"><div class="landing-product__answer-mark"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><p class="landing-product__answer-label">{{ __('messages.accepted_answer') }}</p><p class="landing-product__answer-copy">{{ \Illuminate\Support\Str::limit(trim(strip_tags($spotlight->acceptedAnswer->body)), 125) }}</p><p class="landing-product__answer-author">{{ $spotlight->acceptedAnswer->author?->name }}</p></div></article>
                @else
                    <div class="landing-product__community-note"><span aria-hidden="true">↗</span><span>{{ __('messages.landing_step_discuss_text') }}</span></div>
                @endif
            @else
                <div class="landing-product__empty"><span class="landing-product__empty-icon" aria-hidden="true">?</span><h2>{{ __('messages.landing_step_ask') }}</h2><p>{{ __('messages.landing_questions_empty') }}</p><a href="{{ route('register') }}">{{ __('messages.ask_question') }} <span aria-hidden="true">→</span></a></div>
                <div class="landing-product__footer landing-product__footer--empty"><span>{{ __('messages.landing_built_for_students') }}</span><a href="{{ route('questions.index') }}">{{ __('messages.landing_secondary_cta') }} <span aria-hidden="true">↗</span></a></div>
            @endif
            @if($recentQuestions->isNotEmpty())
                <div class="landing-product__footer"><span><x-user-avatar :user="$spotlight->author" size="sm" />{{ $spotlight->author->name }}</span><a href="{{ route('questions.index') }}" aria-label="{{ __('messages.landing_view_all_questions') }}">{{ __('messages.landing_view_all_questions') }} <span aria-hidden="true">↗</span></a></div>
            @endif
        </div>
    </section>

    <section class="landing-how" id="how-it-works" aria-label="{{ __('messages.landing_how_heading') }}">
        <div class="landing-steps">
            <article class="landing-step"><span class="landing-step__number">01</span><h2>{{ __('messages.landing_feature_questions') }}</h2><p>{{ __('messages.landing_feature_questions_text') }}</p></article>
            <article class="landing-step"><span class="landing-step__number">02</span><h2>{{ __('messages.landing_feature_answers') }}</h2><p>{{ __('messages.landing_feature_answers_text') }}</p></article>
            <article class="landing-step"><span class="landing-step__number">03</span><h2>{{ __('messages.landing_feature_trust') }}</h2><p>{{ __('messages.landing_feature_trust_text') }}</p></article>
        </div>
    </section>

    <section class="landing-stats" aria-label="{{ __('messages.platform_overview') }}">
        @foreach([
            [__('messages.total_questions'), $communityStats['questions']],
            [__('messages.total_answers'), $communityStats['answers']],
            [__('messages.total_students'), $communityStats['students']],
            [__('messages.categories'), $communityStats['categories']],
        ] as [$label, $value])
            <article><span class="landing-stats__value">{{ number_format($value) }}</span><span class="landing-stats__label">{{ $label }}</span></article>
        @endforeach
    </section>

    <section class="landing-capabilities" aria-labelledby="landing-capabilities-heading">
        <div class="landing-section-heading"><p class="landing-kicker">{{ __('messages.landing_features') }}</p><h2 id="landing-capabilities-heading">{{ __('messages.landing_capabilities_heading') }}</h2><p>{{ __('messages.landing_capabilities_intro') }}</p></div>
        <div class="landing-capabilities__grid">
            <article class="landing-feature landing-feature--large"><span class="landing-feature__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M5 5.5h14v10H9l-4 3v-13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8.5 9h7m-7 3h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>{{ __('messages.landing_feature_questions') }}</h3><p>{{ __('messages.landing_feature_questions_text') }}</p><div class="landing-feature__preview"><span>{{ __('messages.category') }}</span><span>{{ __('messages.questions') }}</span><span>{{ __('messages.answers') }}</span></div></article>
            <article class="landing-feature"><span class="landing-feature__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m7 10 5-5 5 5m-5-5v12m-5-2 5 5 5-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>{{ __('messages.landing_feature_voting') }}</h3><p>{{ __('messages.landing_feature_voting_text') }}</p><div class="landing-feature__vote" aria-hidden="true"><span>↑</span><b>{{ __('messages.score') }}</b><span>↓</span></div></article>
            <article class="landing-feature"><span class="landing-feature__icon landing-feature__icon--success" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m5 12 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>{{ __('messages.landing_feature_accepted') }}</h3><p>{{ __('messages.landing_feature_accepted_text') }}</p><span class="landing-feature__accepted">✓ {{ __('messages.accepted_answer') }}</span></article>
            <article class="landing-feature"><span class="landing-feature__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3 14.8 9l6.2.8-4.6 4.4 1.1 6.2-5.5-3-5.5 3 1.1-6.2L3 9.8 9.2 9 12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span><h3>{{ __('messages.landing_feature_reputation') }}</h3><p>{{ __('messages.landing_feature_reputation_text') }}</p><span class="landing-feature__rep">✦ {{ __('messages.reputation') }}</span></article>
            <article class="landing-feature"><span class="landing-feature__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4zM8 9h8M8 12h5M8 15h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3>{{ __('messages.landing_feature_categories') }}</h3><p>{{ __('messages.landing_feature_categories_text') }}</p><a class="landing-feature__link" href="{{ route('community.tags') }}">{{ __('messages.categories') }} <span aria-hidden="true">↗</span></a></article>
            <article class="landing-feature landing-feature--wide"><span class="landing-feature__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 19V9m8 10V5m8 14v-7M2 19h20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3>{{ __('messages.landing_feature_leaderboard') }}</h3><p>{{ __('messages.landing_feature_leaderboard_text') }}</p><a class="landing-feature__link" href="{{ route('community.leaderboard') }}">{{ __('messages.leaderboard') }} <span aria-hidden="true">↗</span></a></article>
        </div>
    </section>

    <section class="landing-questions" aria-labelledby="landing-questions-heading">
        <div class="landing-section-heading landing-section-heading--row"><div><p class="landing-kicker">{{ __('messages.community') }}</p><h2 id="landing-questions-heading">{{ __('messages.landing_questions_heading') }}</h2><p>{{ __('messages.landing_questions_intro') }}</p></div><a class="landing-text-link" href="{{ route('questions.index') }}">{{ __('messages.landing_view_all_questions') }} <span aria-hidden="true">↗</span></a></div>
        @if($recentQuestions->isNotEmpty())
            <div class="landing-question-list">
                @foreach($recentQuestions as $question)
                    <article class="landing-question-card"><a class="landing-question-card__title" href="{{ route('questions.show', $question) }}">{{ $question->title }}</a><p>{{ \Illuminate\Support\Str::limit(trim(strip_tags($question->body)), 165) }}</p><div class="landing-question-card__footer"><span class="landing-question-card__category">{{ $question->category?->name ?? __('messages.categories') }}</span><span class="landing-question-card__author"><x-user-avatar :user="$question->author" size="sm"/>{{ $question->author->name }}</span><span>{{ $question->created_at->diffForHumans() }}</span><span>{{ (int) $question->votes_sum_value }} {{ __('messages.score') }}</span><span class="{{ $question->accepted_answer_id ? 'is-answered' : '' }}">{{ $question->answers_count }} {{ __('messages.answers') }}</span>@if($question->accepted_answer_id)<span class="landing-question-card__accepted">✓ {{ __('messages.accepted_answer') }}</span>@endif</div></article>
                @endforeach
            </div>
        @else
            <div class="landing-question-empty"><span aria-hidden="true">?</span><p>{{ __('messages.landing_questions_empty') }}</p><a class="btn-primary" href="{{ route('register') }}">{{ __('messages.ask_question') }}</a></div>
            @endif
    </section>

    @if($topStudents->isNotEmpty())
        <section class="landing-community" id="community" aria-labelledby="landing-community-heading">
            <div class="landing-section-heading landing-section-heading--row"><div><p class="landing-kicker">{{ __('messages.community') }}</p><h2 id="landing-community-heading">{{ __('messages.landing_community_heading') }}</h2><p>{{ __('messages.landing_community_intro') }}</p></div><a class="landing-text-link" href="{{ route('community.leaderboard') }}">{{ __('messages.leaderboard') }} <span aria-hidden="true">↗</span></a></div>
            <ol class="landing-community__list">
                @foreach($topStudents as $student)
                    @php($metal = ['gold', 'silver', 'bronze'][$loop->index])
                    <li><x-medal-icon :metal="$metal" :rank="$loop->iteration" size="sm"/><x-user-avatar :user="$student" size="sm"/><span class="landing-community__name">{{ $student->name }}</span><strong>{{ number_format($student->reputation) }} <small>{{ __('messages.reputation') }}</small></strong></li>
                @endforeach
            </ol>
        </section>
    @endif

    <section class="landing-final-cta" aria-labelledby="landing-cta-heading"><div><p class="landing-kicker">{{ __('messages.landing_badge') }}</p><h2 id="landing-cta-heading">{{ __('messages.landing_cta_heading') }}</h2><p>{{ __('messages.landing_cta_text') }}</p></div><div class="landing-final-cta__actions"><a class="btn-primary" href="{{ route('register') }}">{{ __('messages.register') }} <span aria-hidden="true">↗</span></a><a class="landing-final-cta__link" href="{{ route('questions.index') }}">{{ __('messages.landing_secondary_cta') }}</a></div></section>
</div>
@endsection
