<?php

namespace App\Services;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;

class PlatformSettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('platform-setting:'.$key, 60, fn () => PlatformSetting::where('key', $key)->value('value') ?? $default);
    }

    public function put(string $key, string $value): void
    {
        PlatformSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('platform-setting:'.$key);
    }
}
