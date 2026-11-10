<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name')) · Student Q&amp;A</title>
    @unless(app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="mr-auto text-xl font-black tracking-tight text-indigo-700">Student Q&amp;A</a>
        <a class="text-sm font-medium hover:text-indigo-700" href="{{ route('home') }}">{{ __('messages.questions') }}</a>
        @auth
            <a class="text-sm font-medium hover:text-indigo-700" href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
            <a class="relative text-sm font-medium hover:text-indigo-700" href="{{ route('notifications.index') }}">
                {{ __('messages.notifications') }}
                @if(auth()->user()->unreadNotifications()->count())
                    <span class="ml-1 rounded-full bg-rose-600 px-1.5 py-0.5 text-xs text-white">{{ auth()->user()->unreadNotifications()->count() }}</span>
                @endif
            </a>
            @if(auth()->user()->isRole('student'))
                <a class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700" href="{{ route('questions.create') }}">{{ __('messages.ask_question') }}</a>
            @endif
            <a class="text-sm text-slate-600" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="text-sm font-semibold text-slate-600 hover:text-rose-700">{{ __('messages.logout') }}</button></form>
        @else
            <a class="text-sm font-medium hover:text-indigo-700" href="{{ route('login') }}">{{ __('messages.login') }}</a>
            <a class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" href="{{ route('register') }}">{{ __('messages.register') }}</a>
        @endauth
        <form method="post" action="{{ route('language.update') }}" class="ml-1">@csrf
            <label class="sr-only" for="locale">{{ __('messages.language') }}</label>
            <select id="locale" name="locale" onchange="this.form.submit()" class="rounded-lg border-slate-300 py-1.5 text-sm">
                @foreach(['tj' => 'Тоҷикӣ', 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
                    <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </nav>
</header>
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @if(session('success'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800" role="alert">
            <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
<footer class="mt-12 border-t border-slate-200 bg-white"><div class="mx-auto max-w-7xl px-4 py-6 text-sm text-slate-500 sm:px-6 lg:px-8">Student Q&amp;A · Variant 24 · Laravel</div></footer>
</body>
</html>
