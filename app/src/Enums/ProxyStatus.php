<?php

declare(strict_types=1);

namespace App\Enums;

class ProxyStatus
{
    public const UNCHECK = 0;
    public const SUCCESS = 1;
    public const FAIL = 2;

    public static function getText(int $status): string
    {
        return match ($status) {
            self::UNCHECK => 'не проверен еще',
            self::SUCCESS => 'работает',
            self::FAIL => 'не работает',
        };
    }
}