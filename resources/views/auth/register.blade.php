@extends('layouts.app')
@section('title', __('messages.register'))
@section('content')
<div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('messages.create_account') }}</h1>
    <form method="post" action="{{ route('register') }}" class="mt-6 space-y-4">@csrf
        <label class="block text-sm font-medium">{{ __('messages.name') }}<input class="mt-1 w-full rounded-xl border-slate-300" name="name" value="{{ old('name') }}" maxlength="120" required autocomplete="name"></label>
        <label class="block text-sm font-medium">{{ __('messages.email') }}<input class="mt-1 w-full rounded-xl border-slate-300" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
        <label class="block text-sm font-medium">{{ __('messages.password') }}<input class="mt-1 w-full rounded-xl border-slate-300" type="password" name="password" minlength="8" required autocomplete="new-password"></label>
        <label class="block text-sm font-medium">{{ __('messages.confirm_password') }}<input class="mt-1 w-full rounded-xl border-slate-300" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></label>
        <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">{{ __('messages.register') }}</button>
    </form>
</div>
@endsection
