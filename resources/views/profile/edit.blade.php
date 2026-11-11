@extends('layouts.app')
@section('title', __('messages.profile'))
@section('content')
<div class="mx-auto max-w-2xl"><h1 class="text-3xl font-black">{{ __('messages.profile') }}</h1>
<form method="post" action="{{ route('profile.update') }}" class="surface mt-6 space-y-5 p-6">@csrf @method('PUT')
    <label class="block text-sm font-semibold text-slate-800">{{ __('messages.name') }}<input name="name" maxlength="120" required class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" value="{{ old('name', auth()->user()->name) }}"></label>
    <label class="block text-sm font-semibold text-slate-800">{{ __('messages.email') }}<input type="email" name="email" required class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" value="{{ old('email', auth()->user()->email) }}"></label>
    <label class="block text-sm font-semibold text-slate-800">{{ __('messages.language') }}<select name="locale" class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5">@foreach(['tj'=>'Тоҷикӣ','ru'=>'Русский','en'=>'English'] as $locale=>$label)<option value="{{ $locale }}" @selected(old('locale', auth()->user()->locale) === $locale)>{{ $label }}</option>@endforeach</select></label>
    <button class="btn-primary" type="submit">{{ __('messages.save_changes') }}</button>
</form></div>
@endsection
