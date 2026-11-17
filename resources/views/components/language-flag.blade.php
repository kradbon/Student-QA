@props(['locale'])
<svg {{ $attributes->class('language-flag') }} viewBox="0 0 24 16" fill="none" aria-hidden="true" focusable="false">
    @switch($locale)
        @case('en')
            <rect width="24" height="16" rx="1.5" fill="#fff"/>
            <path fill="#B22234" d="M0 0h24v1.23H0zm0 2.46h24v1.23H0zm0 2.46h24v1.23H0zm0 2.46h24v1.23H0zm0 2.46h24v1.23H0zm0 2.46h24v1.23H0zm0 2.46h24V16H0z"/>
            <path fill="#3C3B6E" d="M0 0h10.4v8.62H0z"/>
            <g fill="#fff"><circle cx="1.5" cy="1.4" r=".38"/><circle cx="3.5" cy="1.4" r=".38"/><circle cx="5.5" cy="1.4" r=".38"/><circle cx="7.5" cy="1.4" r=".38"/><circle cx="9.5" cy="1.4" r=".38"/><circle cx="2.5" cy="2.8" r=".38"/><circle cx="4.5" cy="2.8" r=".38"/><circle cx="6.5" cy="2.8" r=".38"/><circle cx="8.5" cy="2.8" r=".38"/><circle cx="1.5" cy="4.2" r=".38"/><circle cx="3.5" cy="4.2" r=".38"/><circle cx="5.5" cy="4.2" r=".38"/><circle cx="7.5" cy="4.2" r=".38"/><circle cx="9.5" cy="4.2" r=".38"/><circle cx="2.5" cy="5.6" r=".38"/><circle cx="4.5" cy="5.6" r=".38"/><circle cx="6.5" cy="5.6" r=".38"/><circle cx="8.5" cy="5.6" r=".38"/><circle cx="1.5" cy="7" r=".38"/><circle cx="3.5" cy="7" r=".38"/><circle cx="5.5" cy="7" r=".38"/><circle cx="7.5" cy="7" r=".38"/><circle cx="9.5" cy="7" r=".38"/></g>
        @break
        @case('ru')
            <rect width="24" height="16" rx="1.5" fill="#fff"/>
            <path fill="#0039A6" d="M0 5.33h24v5.34H0z"/>
            <path fill="#D52B1E" d="M0 10.67h24V16H0z"/>
        @break
        @default
            <rect width="24" height="16" rx="1.5" fill="#fff"/>
            <path fill="#C8102E" d="M0 0h24v5.33H0z"/>
            <path fill="#006A44" d="M0 10.67h24V16H0z"/>
            <path fill="#F8F4E3" d="M0 5.33h24v5.34H0z"/>
            <path fill="#F2C94C" d="m9.1 8.05 1.15-.35.3-1.2.55 1.08 1.2.05-.87.83.28 1.18-1.04-.62-1.05.6.3-1.16-.82-.83Zm1.3-2.5h1.1v.42h-1.1zM10 5.55h.6v.42H10zM11 5.55h.6v.42H11z"/>
        @break
    @endswitch
</svg>
