<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'layout'): string
    {
        $viewsPath = dirname(__DIR__, 2) . '/views/';
        $templateFile = $viewsPath . $template . '.php';
        $layoutFile = $viewsPath . $layout . '.php';

        if (!is_file($templateFile) || !is_file($layoutFile)) {
            throw new RuntimeException('View template not found.');
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $templateFile;
        $content = (string) ob_get_clean();

        ob_start();
        require $layoutFile;
        return (string) ob_get_clean();
    }
}
