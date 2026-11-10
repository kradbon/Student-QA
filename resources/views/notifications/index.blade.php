@extends('layouts.app')
@section('title', __('messages.notifications'))
@section('content')
<div class="mx-auto max-w-3xl"><h1 class="text-3xl font-black">{{ __('messages.notifications') }}</h1>
<div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white px-5">@forelse($notifications as $notification)
    <div class="flex items-center gap-4 py-4"><div class="min-w-0 flex-1"><p class="{{ $notification->read_at ? 'text-slate-500' : 'font-semibold text-slate-900' }}">{{ $notification->data['message'] ?? __('messages.notification') }}</p><p class="mt-1 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p></div>@if(!$notification->read_at)<form method="post" action="{{ route('notifications.read', $notification) }}">@csrf<button class="text-sm font-semibold text-indigo-700">{{ __('messages.open') }}</button></form>@else<a class="text-sm font-semibold text-indigo-700" href="{{ $notification->data['url'] ?? '#' }}">{{ __('messages.open') }}</a>@endif</div>
@empty<div class="py-10 text-center text-slate-500">{{ __('messages.no_notifications') }}</div>@endforelse</div><div class="mt-5">{{ $notifications->links() }}</div></div>
@endsection
