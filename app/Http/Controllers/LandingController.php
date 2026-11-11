<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            $dashboard = match (auth()->user()->role) {
                'admin' => 'admin.dashboard',
                'moderator' => 'moderator.dashboard',
                default => 'student.dashboard',
            };

            return redirect()->route($dashboard);
        }

        return view('welcome');
    }
}
