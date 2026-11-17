@props(['metal' => 'bronze', 'rank' => null, 'size' => 'md'])
@php($label = $rank ? __('messages.rank').' '.$rank : __('messages.tier_'.$metal))
<span {{ $attributes->class(['medal-icon', 'medal-icon--'.$metal, 'medal-icon--sm' => $size === 'sm']) }} role="img" aria-label="{{ $label }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path class="medal-ribbon" d="M6.4 2h4.4l2.1 8.3-3.1-1.8-3.1 1.8L6.4 2Zm6.8 0h4.4l-.3 8.3-3.1-1.8-3.1 1.8L13.2 2Z" />
        <circle class="medal-disc" cx="12" cy="15" r="6.2" />
        <path class="medal-star" d="m12 10.9 1.2 2.4 2.7.4-2 1.9.5 2.7-2.4-1.3-2.4 1.3.5-2.7-2-1.9 2.7-.4 1.2-2.4Z" />
    </svg>
    @if($rank)<span class="medal-icon__rank">{{ $rank }}</span>@endif
</span>
