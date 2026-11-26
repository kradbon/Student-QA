<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function html(string $content, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=UTF-8');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $content;
    }

    public static function error(int $status, string $title, string $message, array $headers = []): void
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeMessage = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html = '<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . $safeTitle . '</title><style>body{font:16px system-ui,sans-serif;max-width:680px;margin:12vh auto;padding:24px;color:#182230}h1{font-size:2rem}a{color:#e36b2c}</style></head><body><p>Student Q&amp;A · Day 12 MVC</p><h1>' . $safeTitle . '</h1><p>' . $safeMessage . '</p><p><a href="/">Ба саҳифаи асосӣ</a></p></body></html>';
        self::html($html, $status, $headers);
    }
}
