<?php

declare(strict_types=1);

namespace App\Enums;

class ProxyType
{
    public const NONE = 0;
    public const HTTP = 1;
    public const SOCKS = 2;
    public static function getText(int $type): string
    {
        return match ($type) {
            self::NONE => 'none',
            self::HTTP => 'HTTP',
            self::SOCKS => 'SOCKS',
        };
    }
}