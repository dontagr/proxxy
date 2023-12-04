<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\Exchange\ExchangeExceptionInterface;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GuzzleRequestOptions
{
    public const EXCHANGE_NAME_HEADER = 'X-Exchange-Name';

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $originalRequest, array $options) use ($handler) {
            $options['http_errors'] = 'false';
            $options['on_stats'] = fn (TransferStats $stats) => $this->logExchange($stats);
            if (!array_key_exists('exchange_exception', $options)) {
                throw new \InvalidArgumentException('Guzzle request option "exchange_exception" must be defined');
            }

            $request = $originalRequest->withAddedHeader(
                self::EXCHANGE_NAME_HEADER,
                $options['exchange_exception']::getServiceName()
            );

            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($options, $request) {
                    $statusCode = $response->getStatusCode();
                    if (400 === ($statusCode & 400) || 500 === ($statusCode & 500)) {
                        $this->logUnavailable($options['exchange_exception'], $request, $response);
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * @throws \ReflectionException|ExchangeExceptionInterface
     */
    private function logUnavailable(
        string $exceptionClass,
        RequestInterface $request,
        ResponseInterface $response
    ): void {
        $reflectionClass = new \ReflectionClass($exceptionClass);
        if (!$reflectionClass->implementsInterface(ExchangeExceptionInterface::class)) {
            throw new \InvalidArgumentException('"exchange_exception" must be instance of ExchangeExceptionInterface');
        }

        /** @var ExchangeExceptionInterface $exception */
        $exception = $reflectionClass->newInstance(
            sprintf('service unavailable: %s', $exceptionClass::getServiceName()),
            $request,
            $response
        );

        throw $exception;
    }

    private function logExchange(TransferStats $stats): void
    {
        $request = $stats->getRequest();
        $response = $stats->getResponse();

        $this->logger->info(
            'Exchange Log',
            [
                'execution_time' => $stats->getTransferTime(),
                'service_name' => current($request->getHeader(self::EXCHANGE_NAME_HEADER)),
                'request' => [
                    'method' => $request->getMethod(),
                    'uri' => $request->getUri()->__toString(),
                    'headers' => (new MessageFormatter('{req_headers}'))->format($request),
                    'content' => (new MessageFormatter('{req_body}'))->format($request),
                ],
                'response' => [
                    'code' => $response?->getStatusCode() ?? 0,
                    'headers' => (new MessageFormatter('{res_headers}'))->format($request, $response),
                    'content' => (new MessageFormatter('{res_body}'))->format($request, $response),
                ],
            ]
        );
    }
}
