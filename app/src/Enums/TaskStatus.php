<?php

declare(strict_types=1);

namespace App\Enums;

class TaskStatus
{
    public const PROGRESS = 0;
    public const DONE = 1;

    public static function getText(int $status): string
    {
        return match ($status) {
            self::PROGRESS => 'в процессе',
            self::DONE => 'готово',
        };
    }
}