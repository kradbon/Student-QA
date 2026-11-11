@extends('layouts.app')
@section('title', __('messages.register'))
@section('content')
<div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('messages.create_account') }}</h1>
    <form method="post" action="{{ route('register') }}" class="mt-6 space-y-4">@csrf
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.name') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" name="name" value="{{ old('name') }}" maxlength="120" required autocomplete="name"></label>
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.email') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.password') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" type="password" name="password" minlength="8" required autocomplete="new-password"></label>
        <label class="block text-sm font-medium text-slate-800">{{ __('messages.confirm_password') }}<input class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></label>
        <button class="btn-primary w-full" type="submit">{{ __('messages.register') }}</button>
    </form>
</div>
@endsection
