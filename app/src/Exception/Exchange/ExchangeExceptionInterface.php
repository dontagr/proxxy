<?php

declare(strict_types=1);

namespace App\Exception\Exchange;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ExchangeExceptionInterface extends Throwable
{
    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Throwable $previous = null,
        array $handlerContext = []
    );

    public static function getServiceName(): string;

    public function getRequest(): RequestInterface;

    public function getResponse(): ?ResponseInterface;

    public function hasResponse(): bool;
}
