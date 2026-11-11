<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name')) · Student Q&amp;A</title>
    @unless(app()->environment('testing')) @vite(['resources/css/app.css', 'resources/js/app.js']) @endunless
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex min-h-16 max-w-7xl flex-wrap items-center gap-x-6 gap-y-3 px-4 py-3 sm:px-6 lg:px-8" aria-label="{{ __('messages.main_navigation') }}">
        <a href="{{ route('home') }}" class="mr-auto flex items-center gap-2 text-base font-bold tracking-tight text-slate-950"><span class="grid size-8 place-items-center rounded-lg bg-slate-950 text-[10px] font-bold text-white" aria-hidden="true">Q&amp;A</span>Student Q&amp;A</a>
        <a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a>
        @auth
            <a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
            @if(auth()->user()->isRole('moderator', 'admin'))<a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('moderator.queue') }}">{{ __('messages.moderation') }}</a>@endif
            @if(auth()->user()->isRole('admin'))<a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('admin.dashboard') }}">{{ __('messages.admin_dashboard') }}</a>@endif
            <a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('notifications.index') }}">{{ __('messages.notifications') }}@if($unreadNotificationCount = auth()->user()->unreadNotifications()->count())<span class="ml-1 rounded-full bg-blue-700 px-1.5 py-0.5 text-xs text-white">{{ $unreadNotificationCount }}</span>@endif</a>
            @if(auth()->user()->isRole('student'))<a class="btn-primary min-h-10 px-3 py-2 text-sm" href="{{ route('questions.create') }}">{{ __('messages.ask_question') }}</a>@endif
            <a class="text-sm font-semibold text-slate-700 hover:text-slate-950" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="text-sm font-medium text-slate-600 hover:text-slate-950">{{ __('messages.logout') }}</button></form>
        @else
            <a class="text-sm font-medium text-slate-600 hover:text-slate-950" href="{{ route('login') }}">{{ __('messages.login') }}</a>
            <a class="btn-primary min-h-10 px-3 py-2 text-sm" href="{{ route('register') }}">{{ __('messages.register') }}</a>
        @endauth
        <form method="post" action="{{ route('language.update') }}">@csrf
            <label class="sr-only" for="locale">{{ __('messages.language') }}</label>
            <select id="locale" name="locale" onchange="this.form.submit()" class="min-h-9 rounded-lg border px-2 py-1 text-sm">
                @foreach(['tj' => 'Тоҷикӣ', 'ru' => 'Русский', 'en' => 'English'] as $code => $label)<option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>@endforeach
            </select>
        </form>
    </nav>
</header>
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @if(session('success'))<div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900" role="alert"><p class="font-semibold">{{ __('messages.form_errors') }}</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
<footer class="mt-12 border-t border-slate-200 bg-white"><div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-3 px-4 py-6 text-sm text-slate-500 sm:px-6 lg:px-8"><span>Student Q&amp;A · Variant 24</span><a class="hover:text-slate-900" href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a></div></footer>
</body>
</html>
