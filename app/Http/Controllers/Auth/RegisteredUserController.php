<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PlatformSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(PlatformSettingsService $settings): View
    {
        abort_unless($settings->get('registration_open', '1') === '1', 404);

        return view('auth.register');
    }

    public function store(Request $request, PlatformSettingsService $settings): RedirectResponse
    {
        abort_unless($settings->get('registration_open', '1') === '1', 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([...$data, 'role' => 'student', 'status' => 'active', 'locale' => app()->getLocale()]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', __('messages.registered'));
    }
}
