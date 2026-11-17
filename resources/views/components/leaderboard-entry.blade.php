@props(['member', 'rank', 'isCurrent' => false, 'mode' => 'table'])
@php($medal = [1 => 'gold', 2 => 'silver', 3 => 'bronze'][$rank] ?? null)
@if($mode === 'table')
<tr id="student-row-{{ $member->id }}" @class(['leaderboard-row', 'leaderboard-row--first' => (int) $rank === 1, 'leaderboard-row--current' => $isCurrent])>
    <td class="leaderboard-rank-cell">@if($medal)<x-medal-icon :metal="$medal" :rank="$rank" />@else<span class="leaderboard-rank">{{ $rank }}</span>@endif</td>
    <td><div class="leaderboard-student"><x-user-avatar :user="$member" size="sm" /><span class="leaderboard-student__name">{{ $member->name }}</span><span class="role-badge role-badge--student">{{ __('messages.student') }}</span></div></td>
    <td><x-reputation-badge :points="$member->reputation" /></td>
    <td class="leaderboard-points">{{ number_format($member->reputation) }}</td>
</tr>
@else
<article @class(['leaderboard-mobile-card', 'leaderboard-row--first' => (int) $rank === 1, 'leaderboard-row--current' => $isCurrent])>
    <div class="leaderboard-mobile-card__top">@if($medal)<x-medal-icon :metal="$medal" :rank="$rank" />@else<span class="leaderboard-rank">{{ $rank }}</span>@endif<x-user-avatar :user="$member" size="sm" /><span class="leaderboard-student__name">{{ $member->name }}</span><span class="leaderboard-points">{{ number_format($member->reputation) }}</span></div>
    <div class="leaderboard-mobile-card__meta"><span class="role-badge role-badge--student">{{ __('messages.student') }}</span><x-reputation-badge :points="$member->reputation" /></div>
</article>
@endif
