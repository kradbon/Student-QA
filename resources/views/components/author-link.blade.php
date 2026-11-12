@props(['user'])
<span {{ $attributes->class('inline-flex min-w-0 items-center gap-2 align-middle') }}>
    <x-user-avatar :user="$user" size="sm" />
    @if(auth()->check() && auth()->user()->isRole('moderator', 'admin'))
        <a class="truncate font-semibold text-slate-800 underline decoration-slate-300 underline-offset-2 hover:text-orange-800 hover:decoration-orange-500" href="{{ route('moderator.users.index', ['q' => $user->email]) }}" title="{{ __('messages.manage_accounts') }}: {{ $user->name }}">{{ $user->name }}</a>
    @else
        <span class="truncate font-semibold text-slate-800">{{ $user->name }}</span>
    @endif
</span>
