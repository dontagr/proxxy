<?php

declare(strict_types=1);

namespace App\Exception\Exchange;

use GuzzleHttp\Exception\RequestException;

class IpApiExchangeException extends RequestException implements ExchangeExceptionInterface
{
    public static function getServiceName(): string
    {
        return 'IpApi';
    }
}
