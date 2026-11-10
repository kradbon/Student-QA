<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', ['notifications' => $request->user()->notifications()->paginate(20)]);
    }

    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notification->id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }
}
