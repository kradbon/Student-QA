<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::orderBy('role')->orderBy('name')->paginate(30)]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'moderator', 'admin'])],
            'status' => ['required', Rule::in(['active', 'blocked'])],
        ]);

        if ($request->user()->is($user) && ($data['role'] !== 'admin' || $data['status'] !== 'active')) {
            return back()->with('error', __('messages.cannot_demote_self'));
        }

        if ($user->role === 'admin' && ($data['role'] !== 'admin' || $data['status'] !== 'active')
            && User::where('role', 'admin')->where('status', 'active')->count() <= 1) {
            return back()->with('error', __('messages.last_admin'));
        }

        $user->update($data);

        return back()->with('success', __('messages.user_saved'));
    }
}
