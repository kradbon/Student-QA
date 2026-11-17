@props(['target', 'votes', 'action', 'canVote' => false])
@php
    $currentVote = auth()->check() ? (int) ($votes->firstWhere('user_id', auth()->id())?->value ?? 0) : 0;
    $score = (int) $votes->sum('value');
@endphp
<div class="vote-controls" aria-label="{{ __('messages.vote') }}">
    @if($canVote)
        <form method="post" action="{{ $action }}" class="vote-controls__form">
            @csrf
            <input type="hidden" name="value" value="1">
            <button class="vote-button vote-button--up {{ $currentVote === 1 ? 'is-selected' : '' }}" type="submit" aria-label="{{ __('messages.upvote') }}" aria-pressed="{{ $currentVote === 1 ? 'true' : 'false' }}" title="{{ __('messages.upvote') }}">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 11.5 5.5-5 5.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    @elseif(auth()->guest())
        <a class="vote-button vote-button--up" href="{{ route('login') }}" aria-label="{{ __('messages.login') }}: {{ __('messages.upvote') }}" title="{{ __('messages.login') }} to vote"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 11.5 5.5-5 5.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
    @else
        <span class="vote-button vote-button--up is-disabled" title="{{ __('messages.own_vote') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 11.5 5.5-5 5.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
    @endif
    <span class="vote-score" aria-label="{{ __('messages.score') }} {{ $score }}" title="{{ __('messages.score') }}">{{ $score }}</span>
    @if($canVote)
        <form method="post" action="{{ $action }}" class="vote-controls__form">
            @csrf
            <input type="hidden" name="value" value="-1">
            <button class="vote-button vote-button--down {{ $currentVote === -1 ? 'is-selected' : '' }}" type="submit" aria-label="{{ __('messages.downvote') }}" aria-pressed="{{ $currentVote === -1 ? 'true' : 'false' }}" title="{{ __('messages.downvote') }}">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 8.5 5.5 5 5.5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    @elseif(auth()->guest())
        <a class="vote-button vote-button--down" href="{{ route('login') }}" aria-label="{{ __('messages.login') }}: {{ __('messages.downvote') }}" title="{{ __('messages.login') }} to vote"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 8.5 5.5 5 5.5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
    @else
        <span class="vote-button vote-button--down is-disabled" title="{{ __('messages.own_vote') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 8.5 5.5 5 5.5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
    @endif
</div>
