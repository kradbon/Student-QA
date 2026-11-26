<?php

declare(strict_types=1);

namespace App\Controllers;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home', [
            'pageTitle' => 'Student Q&A — донишро мубодила кунед',
            'activePage' => 'home',
            'stats' => ['questions' => 128, 'answers' => 346, 'students' => 84],
        ]);
    }
}
