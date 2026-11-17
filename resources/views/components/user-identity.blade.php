@props(['user', 'compact' => false])
<div {{ $attributes->class(['user-identity', 'user-identity--compact' => $compact]) }}>
    <x-user-avatar :user="$user" :size="$compact ? 'sm' : 'md'" />
    <div class="user-identity__details">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
            @if(auth()->check() && auth()->user()->isRole('moderator', 'admin'))
                <a class="user-identity__name hover:underline" href="{{ route('moderator.users.index', ['q' => $user->email]) }}" title="{{ __('messages.manage_accounts') }}: {{ $user->name }}">{{ $user->name }}</a>
            @else
                <span class="user-identity__name">{{ $user->name }}</span>
            @endif
            <span class="role-badge role-badge--{{ $user->role }}">{{ __('messages.'.$user->role) }}</span>
        </div>
        <span class="user-reputation"><span aria-hidden="true">✦</span> {{ number_format($user->reputation) }} {{ __('messages.reputation') }}</span>
    </div>
</div>
