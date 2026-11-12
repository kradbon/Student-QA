@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
<div class="mx-auto max-w-2xl"><h1 class="text-2xl font-semibold tracking-tight">{{ __('messages.settings') }}</h1><p class="mt-2 text-slate-600">{{ __('messages.settings_help') }}</p>
<form method="post" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6">@csrf @method('PUT')
    <label class="block text-sm font-semibold">{{ __('messages.default_language') }}<select name="default_locale" class="mt-1 w-full rounded-xl border-slate-300">@foreach(['tj'=>'Тоҷикӣ','ru'=>'Русский','en'=>'English'] as $locale=>$label)<option value="{{ $locale }}" @selected($settings['default_locale'] === $locale)>{{ $label }}</option>@endforeach</select></label>
    <div><input type="hidden" name="registration_open" value="0"><label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="registration_open" value="1" @checked($settings['registration_open'])>{{ __('messages.open_registration') }}</label></div>
    <label class="block text-sm font-semibold">{{ __('messages.max_file_size') }}<input type="number" min="1" max="20" required name="upload_max_mb" value="{{ $settings['upload_max_mb'] }}" class="mt-1 w-full rounded-xl border-slate-300"></label>
    <button class="rounded-xl bg-orange-700 px-5 py-3 font-semibold text-white">{{ __('messages.save_changes') }}</button>
</form></div>
@endsection
