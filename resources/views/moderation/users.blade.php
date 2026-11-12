@extends('layouts.app')
@section('title', __('messages.manage_accounts'))
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">{{ __('messages.moderation') }}</p><h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">{{ __('messages.manage_accounts') }}</h1><p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.moderator_accounts_help') }}</p></div></div>
<form method="get" action="{{ route('moderator.users.index') }}" class="surface mt-6 flex flex-wrap items-end gap-3 p-4"><label class="min-w-64 flex-1 text-sm font-semibold text-slate-800">{{ __('messages.search_users') }}<input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_users_placeholder') }}" class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5"></label><button class="btn-primary" type="submit">{{ __('messages.search') }}</button>@if(request('q'))<a class="btn-secondary" href="{{ route('moderator.users.index') }}">{{ __('messages.clear') }}</a>@endif</form>
<div class="surface mt-5 overflow-hidden"><div class="divide-y divide-slate-200">
@forelse($users as $user)
    <article class="grid gap-4 p-5 xl:grid-cols-[minmax(14rem,1fr)_minmax(22rem,1.1fr)] xl:items-center">
        <div class="flex min-w-0 items-center gap-3"><x-user-avatar :user="$user" size="md"/><div class="min-w-0"><h2 class="truncate font-semibold text-slate-950">{{ $user->name }}</h2><p class="truncate text-sm text-slate-600">{{ $user->email }}</p><span @class(['mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-white text-slate-700 ring-1 ring-slate-300' => $user->status === 'active', 'bg-orange-100 text-orange-950' => $user->status !== 'active'])>{{ __('messages.status_'.$user->status) }}</span></div></div>
        <form method="post" action="{{ route('moderator.users.action', $user) }}" class="grid gap-2 sm:grid-cols-[minmax(9rem,.6fr)_minmax(12rem,1fr)_auto]">@csrf
            <label class="sr-only" for="decision-{{ $user->id }}">{{ __('messages.account_action') }}</label><select id="decision-{{ $user->id }}" name="decision" required class="min-h-10 rounded-lg border px-3 py-2 text-sm"><option value="" disabled selected>{{ __('messages.choose_action') }}</option>@if($user->status === 'active')<option value="suspend">{{ __('messages.suspend_account') }}</option><option value="ban">{{ __('messages.ban_account') }}</option>@else<option value="restore">{{ __('messages.restore_account') }}</option>@endif</select>
            <label class="sr-only" for="note-{{ $user->id }}">{{ __('messages.action_note') }}</label><input id="note-{{ $user->id }}" name="note" maxlength="1000" placeholder="{{ __('messages.action_note_optional') }}" class="min-h-10 rounded-lg border px-3 py-2 text-sm">
            <button class="btn-primary min-h-10 px-3 py-2 text-sm" type="submit">{{ __('messages.apply_action') }}</button>
        </form>
    </article>
@empty<div class="p-10 text-center"><p class="font-semibold text-slate-900">{{ __('messages.no_users_found') }}</p><p class="mt-1 text-sm text-slate-600">{{ __('messages.no_users_found_help') }}</p></div>@endforelse
</div></div><div class="mt-5">{{ $users->links() }}</div>
@endsection
