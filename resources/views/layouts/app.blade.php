<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name')) · Student Q&amp;A</title>
    <script>
        (() => {
            try {
                const saved = localStorage.getItem('student-qa-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = saved === 'dark' || saved === 'light' ? saved : (prefersDark ? 'dark' : 'light');
            } catch {
                document.documentElement.dataset.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
        })();
    </script>
    @unless(app()->environment('testing')) @vite(['resources/css/app.css', 'resources/js/app.js']) @endunless
</head>
<body class="min-h-screen text-slate-900 antialiased">
@php($publicLanding = request()->routeIs('home') && auth()->guest())
<header class="app-header sticky top-0 z-30 border-b border-neutral-800 bg-neutral-950 text-white shadow-sm">
    <div class="mx-auto flex h-[60px] max-w-[1800px] items-center gap-5 px-4 sm:px-6 lg:px-8">
        <a href="{{ $publicLanding ? route('home') : route('questions.index') }}" class="app-brand flex shrink-0 items-center gap-3 font-bold tracking-tight text-white"><span class="grid size-9 place-items-center rounded-lg bg-orange-500 text-[10px] font-extrabold text-white">Q&amp;A</span><span>Students QA</span></a>
        @if($publicLanding)
            <nav class="app-public-nav ml-auto hidden items-center gap-1 lg:flex" aria-label="{{ __('messages.main_navigation') }}"><a href="{{ route('home') }}" aria-current="page">{{ __('messages.landing_home') }}</a><a href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a><a href="{{ route('community.tags') }}">{{ __('messages.categories') }}</a><a href="{{ route('community.leaderboard') }}">{{ __('messages.leaderboard') }}</a><a href="{{ route('login') }}">{{ __('messages.login') }}</a><a class="btn-primary" href="{{ route('register') }}">{{ __('messages.register') }}</a></nav>
            <details class="app-mobile-public-menu relative ml-auto lg:hidden"><summary aria-label="{{ __('messages.open_navigation') }}">☰</summary><nav aria-label="{{ __('messages.main_navigation') }}"><a href="{{ route('home') }}">{{ __('messages.landing_home') }}</a><a href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a><a href="{{ route('community.tags') }}">{{ __('messages.categories') }}</a><a href="{{ route('community.leaderboard') }}">{{ __('messages.leaderboard') }}</a><a href="{{ route('login') }}">{{ __('messages.login') }}</a><a href="{{ route('register') }}">{{ __('messages.register') }}</a></nav></details>
        @endif
        @unless($publicLanding)<form method="get" action="{{ route('questions.index') }}" class="mx-auto hidden w-full max-w-2xl lg:block"><label class="relative block"><span class="sr-only">{{ __('messages.search') }}</span><svg class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input class="h-11 w-full rounded-lg border border-white/15 bg-white/15 pl-11 pr-4 text-sm text-white placeholder:text-white/70 focus:border-white/40 focus:bg-white/20" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search') }}"></label></form>@endunless
        <div class="ml-auto flex items-center gap-3">
            @auth
                @if(auth()->user()->isRole('student'))<details class="relative hidden sm:block"><summary class="flex h-10 cursor-pointer list-none items-center gap-2 rounded-lg border border-white/30 px-3 text-sm font-semibold hover:bg-white/10"><span aria-hidden="true">+</span>{{ __('messages.ask_question') }}<span aria-hidden="true">⌄</span></summary><div class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 bg-white p-1.5 text-slate-800 shadow-xl"><a class="block rounded-md px-3 py-2 text-sm hover:bg-slate-100" href="{{ route('student.questions.create') }}">{{ __('messages.ask_question') }}</a></div></details>@endif
                <a href="{{ route('notifications.index') }}" class="relative grid size-10 place-items-center rounded-lg text-white hover:bg-white/10" aria-label="{{ __('messages.notifications') }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unreadCount = auth()->user()->unreadNotifications()->count())<span class="absolute right-1 top-1 size-2.5 rounded-full border-2 border-neutral-950 bg-orange-400"></span>@endif</a>
                <a href="{{ route('community.leaderboard') }}" class="hidden size-10 place-items-center rounded-lg text-white hover:bg-white/10 sm:grid" aria-label="{{ __('messages.leaderboard') }}"><x-nav-icon name="leaderboard" class="text-white" /></a>

                <details class="relative"><summary class="flex cursor-pointer list-none items-center gap-2 rounded-full p-1 hover:bg-white/10"><x-user-avatar :user="auth()->user()" size="sm"/><span class="hidden max-w-28 truncate text-sm font-semibold md:inline">{{ auth()->user()->name }}</span></summary><div class="absolute right-0 mt-2 w-52 rounded-lg border border-slate-200 bg-white p-1.5 text-slate-800 shadow-xl"><a class="block rounded-md px-3 py-2 text-sm hover:bg-slate-100" href="{{ route('profile.edit') }}">{{ __('messages.profile') }}</a>@if(auth()->user()->isRole('student'))<a class="block rounded-md px-3 py-2 text-sm hover:bg-slate-100" href="{{ route('student.questions') }}">{{ __('messages.my_questions') }}</a>@endif<a class="block rounded-md px-3 py-2 text-sm hover:bg-slate-100" href="{{ route('settings') }}">{{ __('messages.settings') }}</a><form method="post" action="{{ route('logout') }}">@csrf<button class="block w-full rounded-md px-3 py-2 text-left text-sm hover:bg-slate-100">{{ __('messages.logout') }}</button></form></div></details>
            @endauth
            <details class="language-menu relative">
                <summary class="language-toggle" aria-label="{{ __('messages.language') }}: {{ strtoupper(app()->getLocale()) }}" title="{{ __('messages.language') }}" aria-haspopup="menu"><svg class="language-toggle__globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.5 9h17M3.5 15h17M12 3c2.2 2.4 3.2 5.4 3.2 9s-1 6.6-3.2 9c-2.2-2.4-3.2-5.4-3.2-9S9.8 5.4 12 3Z"/></svg><span>{{ strtoupper(app()->getLocale()) }}</span></summary>
                <div class="language-menu__panel" role="menu" aria-label="{{ __('messages.language') }}">
                    @foreach(['tj' => __('messages.language_tajik'), 'ru' => __('messages.language_russian'), 'en' => 'English'] as $code => $label)
                        <form method="post" action="{{ route('language.update') }}">@csrf<input type="hidden" name="locale" value="{{ $code }}"><button type="submit" role="menuitem" @if(app()->getLocale() === $code) aria-current="true" @endif><span>{{ $label }}</span><span class="language-menu__code">{{ strtoupper($code) }}</span>@if(app()->getLocale() === $code)<span class="language-menu__check" aria-hidden="true">&#10003;</span>@endif</button></form>
                    @endforeach
                </div>
            </details>
            <button type="button" class="theme-toggle" data-theme-toggle data-theme-label-dark="{{ __('messages.switch_to_dark_theme') }}" data-theme-label-light="{{ __('messages.switch_to_light_theme') }}" aria-label="{{ __('messages.toggle_theme') }}" title="{{ __('messages.toggle_theme') }}" aria-pressed="false"><svg class="theme-toggle__moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.2 15.1A8.5 8.5 0 0 1 8.9 3.8 8.5 8.5 0 1 0 20.2 15.1Z"/></svg><svg class="theme-toggle__sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg></button>
            @unless($publicLanding)<details class="relative lg:hidden"><summary class="grid size-10 cursor-pointer list-none place-items-center rounded-lg border border-slate-300 text-slate-800 hover:bg-slate-50" aria-label="{{ __('messages.open_navigation') }}"><span class="text-lg" aria-hidden="true">☰</span></summary><div class="absolute right-0 mt-2 max-h-[75vh] w-72 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3 shadow-xl">@include('layouts.navigation')</div></details>@endunless
        </div>
    </div>
</header>
<div class="mx-auto flex max-w-[1800px] items-start lg:min-h-[calc(100vh-3.75rem)]">
    @unless($publicLanding)<aside class="app-sidebar sticky top-[60px] hidden h-[calc(100vh-3.75rem)] w-[230px] shrink-0 overflow-y-auto border-r border-slate-200 bg-white px-4 py-6 lg:block">@include('layouts.navigation')</aside>@endunless
    <div class="min-w-0 flex-1">
            <main @class(['app-main mx-auto min-h-[calc(100vh-3.75rem)] max-w-[1600px] px-4 py-7 sm:px-6 lg:px-8 lg:py-8', 'page-landing' => $publicLanding, 'page-student-dashboard' => request()->routeIs('student.dashboard'), 'page-admin-dashboard' => request()->routeIs('admin.dashboard'), 'page-moderator-dashboard' => request()->routeIs('moderator.dashboard'), 'page-questions' => request()->routeIs('questions.index'), 'page-question-detail' => request()->routeIs('questions.show'), 'page-moderation' => request()->routeIs('moderator.queue', 'moderator.history', 'moderator.content', 'moderator.users.*'), 'page-admin-management' => request()->routeIs('admin.users.*', 'admin.content.*', 'admin.categories.*', 'admin.settings.*'), 'page-profile' => request()->routeIs('profile.*'), 'page-community-users' => request()->routeIs('community.users'), 'page-community-tags' => request()->routeIs('community.tags'), 'page-leaderboard' => request()->routeIs('community.leaderboard')])>
            <form method="get" action="{{ route('questions.index') }}" class="mb-5 lg:hidden"><label class="relative block"><span class="sr-only">{{ __('messages.search') }}</span><svg class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input class="min-h-11 w-full border-slate-300 pl-10" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search') }}"></label></form>
            @if(session('success'))<div class="mb-5 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-medium text-orange-950" role="status">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-5 rounded-lg border border-orange-300 bg-orange-50 px-4 py-3 text-sm font-medium text-orange-950" role="alert">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="mb-5 rounded-lg border border-orange-300 bg-orange-50 px-4 py-3 text-sm text-orange-950" role="alert"><p class="font-semibold">{{ __('messages.form_errors') }}</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
        @if($publicLanding)
            <footer class="site-footer site-footer--landing">
                <div class="site-footer__grid">
                    <div class="site-footer__brand"><a href="{{ route('home') }}" class="app-brand"><span>Q&amp;A</span><strong>Students QA</strong></a><p>{{ __('messages.landing_footer_description') }}</p></div>
                    <nav aria-label="{{ __('messages.questions') }}"><h2>{{ __('messages.landing_features') }}</h2><a href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a><a href="{{ route('community.tags') }}">{{ __('messages.categories') }}</a><a href="{{ route('community.leaderboard') }}">{{ __('messages.leaderboard') }}</a></nav>
                    <nav aria-label="{{ __('messages.account') }}"><h2>{{ __('messages.account') }}</h2><a href="{{ route('login') }}">{{ __('messages.login') }}</a><a href="{{ route('register') }}">{{ __('messages.register') }}</a></nav>
                </div>
                <div class="site-footer__bottom"><span>© {{ date('Y') }} Students QA · Variant 24</span><a href="{{ route('home') }}">{{ __('messages.landing_home') }}</a></div>
            </footer>
        @else
            <footer class="mt-12 border-t border-slate-200 bg-white"><div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-3 px-4 py-6 text-sm text-slate-500 sm:px-6 lg:px-8"><span>Student Q&amp;A · Variant 24</span><a class="hover:text-slate-950" href="{{ route('questions.index') }}">{{ __('messages.questions') }}</a></div></footer>
        @endif
    </div>
</div>
</body>
</html>
