<?php

declare(strict_types=1);

namespace App\Core;

final class Text
{
    public static function firstCharacter(string $value): string
    {
        return preg_match('/^./us', $value, $matches) === 1 ? $matches[0] : '?';
    }
}
