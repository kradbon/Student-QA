<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlatformSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformSettingsController extends Controller
{
    public function edit(PlatformSettingsService $settings): View
    {
        return view('admin.settings', [
            'settings' => [
                'default_locale' => $settings->get('default_locale', 'ru'),
                'registration_open' => $settings->get('registration_open', '1') === '1',
                'upload_max_mb' => $settings->get('upload_max_mb', '5'),
            ],
        ]);
    }

    public function update(Request $request, PlatformSettingsService $settings): RedirectResponse
    {
        $data = $request->validate([
            'default_locale' => ['required', 'in:tj,ru,en'],
            'registration_open' => ['required', 'in:0,1'],
            'upload_max_mb' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        foreach ($data as $key => $value) {
            $settings->put($key, (string) $value);
        }

        return back()->with('success', __('messages.settings_saved'));
    }
}
