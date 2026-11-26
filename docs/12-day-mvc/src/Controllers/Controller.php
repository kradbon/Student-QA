<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;

abstract class Controller
{
    protected function render(string $template, array $data = [], int $status = 200): void
    {
        Response::html(View::render($template, $data), $status);
    }
}
