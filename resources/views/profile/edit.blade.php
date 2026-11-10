@extends('layouts.app')
@section('title', __('messages.profile'))
@section('content')
<div class="mx-auto max-w-2xl"><h1 class="text-3xl font-black">{{ __('messages.profile') }}</h1>
<form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6">@csrf @method('PUT')
    <label class="block text-sm font-semibold">{{ __('messages.name') }}<input name="name" maxlength="120" required class="mt-1 w-full rounded-xl border-slate-300" value="{{ old('name', auth()->user()->name) }}"></label>
    <label class="block text-sm font-semibold">{{ __('messages.email') }}<input type="email" name="email" required class="mt-1 w-full rounded-xl border-slate-300" value="{{ old('email', auth()->user()->email) }}"></label>
    <label class="block text-sm font-semibold">{{ __('messages.language') }}<select name="locale" class="mt-1 w-full rounded-xl border-slate-300">@foreach(['tj'=>'Тоҷикӣ','ru'=>'Русский','en'=>'English'] as $locale=>$label)<option value="{{ $locale }}" @selected(old('locale', auth()->user()->locale) === $locale)>{{ $label }}</option>@endforeach</select></label>
    <button class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">{{ __('messages.save_changes') }}</button>
</form></div>
@endsection
