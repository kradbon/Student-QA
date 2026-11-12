@props(['name'])
<svg {{ $attributes->class('size-5 shrink-0') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('dashboard')<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/>@break
        @case('questions')<path d="M7 3.5h7l4 4V20a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2V5.5a2 2 0 0 1 2-2Z"/><path d="M14 3.5V8h4M8.5 12h7M8.5 16h7"/>@break
        @case('plus')<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>@break
        @case('bell')<path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4"/>@break
        @case('user')<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>@break
        @case('moderation')<path d="m12 3 8 3v5c0 5-3.4 8-8 10-4.6-2-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/>@break
        @case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 11a4 4 0 0 0 0-8M21 21v-2a4 4 0 0 0-3-3.87"/>@break
        @case('history')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2M4 5v4h4"/>@break
        @case('tag')<path d="M20 13 13 20a2 2 0 0 1-3 0l-6-6V4h10l6 6a2 2 0 0 1 0 3Z"/><circle cx="8" cy="8" r="1"/>@break
        @case('settings')<circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.1.9-1.1 1.9-1.4-.5a8 8 0 0 1-1.5.9l-.2 1.5h-2.2l-.3-1.5a8 8 0 0 1-1.6-.9l-1.4.5-1.1-1.9 1.1-.9a7 7 0 0 1 0-1.8l-1.1-.9 1.1-1.9 1.4.5a8 8 0 0 1 1.6-.9l.3-1.5h2.2l.2 1.5a8 8 0 0 1 1.5.9l1.4-.5 1.1 1.9-1.1.9a7 7 0 0 1-.1 1.7Z"/>@break
        @case('content')<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>@break
        @case('flag')<path d="M5 21V4m0 1c6-4 8 4 14 0v10c-6 4-8-4-14 0"/>@break
        @case('logout')<path d="M10 17l5-5-5-5M15 12H3M12 3h6a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-6"/>@break
        @default<circle cx="12" cy="12" r="9"/>
    @endswitch
</svg>
