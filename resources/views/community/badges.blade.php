@extends('layouts.app')
@section('title', __('messages.reputation'))
@section('content')
<div class="border-b border-slate-200 pb-4">
    <p class="eyebrow">{{ __('messages.community') }}</p>
    <h1 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('messages.reputation') }}</h1>
    <p class="mt-1 text-sm text-slate-600">{{ __('messages.dashboard_intro') }}</p>
</div>

<div class="mt-5 flex flex-wrap items-center gap-x-8 gap-y-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
    <div class="flex items-center gap-2 text-sm"><span class="size-2 rounded-full bg-slate-400"></span><span class="font-semibold text-slate-800">Bronze</span><span class="text-slate-500">0-99</span></div>
    <div class="flex items-center gap-2 text-sm"><span class="size-2 rounded-full bg-blue-500"></span><span class="font-semibold text-slate-800">Silver</span><span class="text-slate-500">100-999</span></div>
    <div class="flex items-center gap-2 text-sm"><span class="size-2 rounded-full bg-amber-500"></span><span class="font-semibold text-slate-800">Gold</span><span class="text-slate-500">1,000+</span></div>
</div>

<section class="mt-6">
    <h2 class="mb-3 text-base font-semibold text-slate-900">{{ __('messages.total_reputation') }}</h2>
    <div class="surface overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($contributors as $rank => $member)
                <div class="flex min-h-14 items-center gap-3 px-4 py-2">
                    <span @class([
                        'grid size-7 shrink-0 place-items-center rounded-full text-xs font-semibold tabular-nums',
                        'bg-amber-50 text-amber-800' => $rank === 0,
                        'bg-slate-100 text-slate-500' => $rank !== 0,
                    ])>{{ $rank + 1 }}</span>
                    <x-user-avatar :user="$member" size="sm"/>
                    <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-800">{{ $member->name }}</span>
                    <span class="text-sm font-semibold tabular-nums text-slate-700">{{ number_format($member->reputation) }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-sm text-slate-500">{{ __('messages.no_users_found') }}</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
