@props(['points'])
@php
    $tier = $points >= 1000 ? 'gold' : ($points >= 100 ? 'silver' : 'bronze');
@endphp
<span {{ $attributes->class(['reputation-tier', 'reputation-tier--'.$tier]) }}>
    <x-medal-icon :metal="$tier" size="sm" aria-hidden="true" />{{ __('messages.tier_'.$tier) }}
</span>
