@extends('layouts.app')
@section('title', __('messages.leaderboard'))
@section('content')
<div class="mx-auto max-w-6xl">
    <header class="leaderboard-header">
        <div><p class="eyebrow">{{ __('messages.community') }}</p><h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">{{ __('messages.student_rankings') }}</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.leaderboard_intro') }}</p></div>
        <span class="leaderboard-period"><span class="leaderboard-period__dot"></span>{{ __('messages.all_time') }}</span>
    </header>

    <section class="mt-8" aria-labelledby="podium-title">
        <div class="mb-5"><p class="eyebrow">{{ __('messages.top_contributors') }}</p><h2 id="podium-title" class="mt-1 text-xl font-semibold text-slate-900">{{ __('messages.top_students') }}</h2></div>
        @if($podium->isNotEmpty())
            <div class="podium-grid">
                @if(isset($podium[1]))<x-podium-card :user="$podium[1]" :place="2" />@endif
                @if(isset($podium[0]))<x-podium-card :user="$podium[0]" :place="1" />@endif
                @if(isset($podium[2]))<x-podium-card :user="$podium[2]" :place="3" />@endif
            </div>
        @else
            <div class="leaderboard-empty">{{ __('messages.no_students_ranked') }}</div>
        @endif
    </section>

    @if(auth()->check() && auth()->user()->isRole('student') && auth()->user()->status === 'active')
        <section class="my-achievement mt-7" aria-labelledby="my-achievement-title">
            <div class="my-achievement__identity"><span class="my-achievement__icon" aria-hidden="true">✦</span><div><h2 id="my-achievement-title">{{ __('messages.my_achievement') }}</h2><p>{{ __('messages.your_community_position') }}</p></div></div>
            <div class="my-achievement__rank"><span>{{ __('messages.current_rank') }}</span><strong>#{{ $myRank }}</strong></div>
            <div class="my-achievement__points"><strong>{{ number_format(auth()->user()->reputation) }}</strong><span>{{ __('messages.reputation_points') }}</span></div>
            <x-reputation-badge :points="auth()->user()->reputation" />
            <x-reputation-progress :points="auth()->user()->reputation" />
            <a class="my-achievement__link" href="{{ route('community.leaderboard', ['page' => max(1, (int) ceil($myRank / 20))]) }}#ranking-title">{{ __('messages.find_my_position') }} <span aria-hidden="true">→</span></a>
        </section>
    @elseif(auth()->guest())
        <section class="my-achievement my-achievement--guest mt-7" aria-labelledby="my-achievement-title">
            <div class="my-achievement__identity"><span class="my-achievement__icon" aria-hidden="true">✦</span><div><h2 id="my-achievement-title">{{ __('messages.my_achievement') }}</h2><p>{{ __('messages.join_to_see_position') }}</p></div></div>
            <div class="flex flex-wrap gap-2"><a class="btn-secondary min-h-10 px-4 py-2 text-sm" href="{{ route('login') }}">{{ __('messages.login') }}</a><a class="btn-primary min-h-10 px-4 py-2 text-sm" href="{{ route('register') }}">{{ __('messages.register') }}</a></div>
        </section>
    @endif

    <section class="mt-9" aria-labelledby="ranking-title">
        <div class="leaderboard-table-heading">
            <div><p class="eyebrow">{{ __('messages.all_time') }}</p><h2 id="ranking-title" class="mt-1 text-xl font-semibold text-slate-950">{{ __('messages.student_leaderboard') }}</h2></div>
            <form class="leaderboard-search" method="get" action="{{ route('community.leaderboard') }}"><label class="sr-only" for="leaderboard-search">{{ __('messages.search_students') }}</label><input id="leaderboard-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('messages.search_students') }}"><button class="btn-secondary min-h-10 px-3 py-2 text-sm" type="submit">{{ __('messages.search') }}</button></form>
        </div>
        <div id="ranking-table" class="leaderboard-table-wrap">
            <table class="leaderboard-table">
                <thead><tr><th scope="col">{{ __('messages.rank') }}</th><th scope="col">{{ __('messages.student') }}</th><th scope="col">{{ __('messages.achievement') }}</th><th scope="col" class="text-right">{{ __('messages.total_points') }}</th></tr></thead>
                <tbody>
                @forelse($leaders as $index => $member)
                    <x-leaderboard-entry :member="$member" :rank="$member->leaderboard_rank" :is-current="auth()->id() === $member->id" mode="table" />
                @empty
                    <tr><td colspan="4" class="leaderboard-empty">{{ __('messages.no_students_ranked') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="leaderboard-mobile-list">
            @foreach($leaders as $index => $member)
                <x-leaderboard-entry :member="$member" :rank="$member->leaderboard_rank" :is-current="auth()->id() === $member->id" mode="mobile" />
            @endforeach
        </div>
        <div class="mt-5">{{ $leaders->links() }}</div>
    </section>
</div>
@endsection
