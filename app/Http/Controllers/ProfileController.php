<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($request->user()->id)],
            'locale' => ['required', 'in:tj,ru,en'],
        ]);

        $request->user()->update($data);
        $request->session()->put('locale', $data['locale']);

        return back()->with('success', __('messages.profile_saved'));
    }
}
