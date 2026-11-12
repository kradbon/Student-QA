<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($search = trim((string) $request->query('q')), fn ($query) => $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->orderBy('role')->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'moderator', 'admin'])],
            'status' => ['required', Rule::in(['active', 'suspended', 'blocked'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->user()->is($user) && ($data['role'] !== 'admin' || $data['status'] !== 'active')) {
            return back()->with('error', __('messages.cannot_demote_self'));
        }

        if ($user->role === 'admin' && ($data['role'] !== 'admin' || $data['status'] !== 'active')
            && User::where('role', 'admin')->where('status', 'active')->count() <= 1) {
            return back()->with('error', __('messages.last_admin'));
        }

        $oldRole = $user->role;
        $oldStatus = $user->status;
        DB::transaction(function () use ($request, $user, $data, $oldRole, $oldStatus): void {
            $user->update(collect($data)->except('note')->all());
            if ($oldRole !== $user->role || $oldStatus !== $user->status) {
                $user->moderationLogs()->create([
                    'moderator_id' => $request->user()->id,
                    'action' => 'admin_account_update',
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        if ($oldRole !== $user->role || $oldStatus !== $user->status) {
            $user->notify(new PlatformNotification(__('messages.account_admin_update_notice'), route('profile.edit'), 'account_status'));
        }

        return back()->with('success', __('messages.user_saved'));
    }
}
