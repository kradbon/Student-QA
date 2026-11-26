<?php

declare(strict_types=1);

namespace App\Controllers;

final class ReferenceController extends Controller
{
    public function index(): void
    {
        $this->render('reference', [
            'pageTitle' => 'Маълумотнома',
            'activePage' => 'reference',
        ]);
    }
}
