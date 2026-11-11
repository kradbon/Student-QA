@extends('layouts.app')
@section('title', __('messages.login'))
@section('content')
<div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('messages.welcome_back') }}</h1>
    <form method="post" action="{{ route('login') }}" class="mt-6 space-y-4">@csrf
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.email') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.password') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" type="password" name="password" required autocomplete="current-password"></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded border-slate-300">{{ __('messages.remember_me') }}</label>
        <button class="btn-primary w-full" type="submit">{{ __('messages.login') }}</button>
    </form>
    <p class="mt-5 text-sm text-slate-600">{{ __('messages.no_account') }} <a class="font-semibold text-indigo-700" href="{{ route('register') }}">{{ __('messages.register') }}</a></p>
</div>
@endsection
