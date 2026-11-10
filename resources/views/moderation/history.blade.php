@extends('layouts.app')
@section('title', __('messages.moderation_history'))
@section('content')
<h1 class="text-3xl font-black">{{ __('messages.moderation_history') }}</h1><div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white px-5">@forelse($logs as $log)<div class="py-4"><p><strong>{{ $log->moderator->name }}</strong> · {{ __('messages.action_'.$log->action) }}</p>@if($log->note)<p class="mt-1 text-sm text-slate-600">{{ $log->note }}</p>@endif<p class="mt-1 text-xs text-slate-400">{{ $log->created_at->format('Y-m-d H:i') }}</p></div>@empty<div class="py-8 text-slate-500">{{ __('messages.no_moderation') }}</div>@endforelse</div><div class="mt-5">{{ $logs->links() }}</div>
@endsection
