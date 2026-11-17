@props(['points'])
@php
    $tier = $points >= 1000 ? 'gold' : ($points >= 100 ? 'silver' : 'bronze');
@endphp
<span {{ $attributes->class(['reputation-tier', 'reputation-tier--'.$tier]) }}>
    <span aria-hidden="true">✦</span>{{ __('messages.tier_'.$tier) }}
</span>
