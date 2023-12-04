<?php

namespace App\Logger;

use Monolog\LogRecord;

class RequestIdProcessor
{
    public function __construct(private readonly array $appVersion)
    {
    }

    public function __invoke(LogRecord $record): array|LogRecord
    {
        $record['extra']['tag'] = $this->appVersion['tag'];
        $record['extra']['hash'] = $this->appVersion['hash'];

        return $record;
    }
}
