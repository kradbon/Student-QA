@props(['points'])
@if($points < 1000)
    @php
        $nextTierAt = $points >= 100 ? 1000 : 100;
        $tierBase = $points >= 100 ? 100 : 0;
        $tierProgress = min(100, max(0, (($points - $tierBase) / ($nextTierAt - $tierBase)) * 100));
        $pointsRemaining = max(0, $nextTierAt - $points);
    @endphp
    <div {{ $attributes->class('reputation-progress') }}>
        <div class="reputation-progress__caption"><span>{{ __('messages.points_to_next_tier', ['points' => $pointsRemaining]) }}</span><span>{{ (int) round($tierProgress) }}%</span></div>
        <div class="reputation-progress__track" role="progressbar" aria-label="{{ __('messages.points_to_next_tier', ['points' => $pointsRemaining]) }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) round($tierProgress) }}"><span style="width: {{ $tierProgress }}%"></span></div>
    </div>
@else
    <span {{ $attributes->class('reputation-progress__complete') }}>{{ __('messages.highest_tier_reached') }}</span>
@endif
