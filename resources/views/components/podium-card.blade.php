@props(['user', 'place'])
@php($medal = ['1' => 'gold', '2' => 'silver', '3' => 'bronze'][$place] ?? 'bronze')
<article class="podium-card podium-card--{{ $medal }} {{ $place === 1 ? 'podium-card--first' : '' }}">
    <div class="podium-card__rank"><x-medal-icon :metal="$medal" :rank="$place" /></div>
    <x-user-avatar :user="$user" size="lg" class="podium-card__avatar" />
    <h3 class="podium-card__name" title="{{ $user->name }}">{{ $user->name }}</h3>
    <p class="podium-card__points">{{ number_format($user->reputation) }} <span>{{ __('messages.reputation') }}</span></p>
    <x-reputation-badge :points="$user->reputation" />
</article>
