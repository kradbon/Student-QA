<?php

namespace App\Http\Middleware;

use App\Services\PlatformSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private readonly PlatformSettingsService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        $default = $this->settings->get('default_locale', 'tj');
        $locale = $request->user()?->locale ?? $request->session()->get('locale', $default);
        app()->setLocale(in_array($locale, ['tj', 'ru', 'en'], true) ? $locale : 'tj');

        return $next($request);
    }
}
