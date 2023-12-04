<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Exchange\IpApiResponce;
use App\Entity\Proxy;
use App\Exception\Exchange\IpApiExchangeException;
use App\Exception\ServiceParameterException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class IpApiClient
{
    public function __construct(
        private readonly Client $client,
        private readonly SerializerInterface $serializer,
        private readonly array $options,
    ) {
        if (!isset($this->options['endpoint'])) {
            throw new ServiceParameterException('IpApi: endpoint must be defined');
        }
    }

    public function getInfoByProxy(Proxy $proxy): IpApiResponce {
        $options = [
            RequestOptions::SYNCHRONOUS => true,
            RequestOptions::VERIFY => false,
            'exchange_exception' => IpApiExchangeException::class,
        ];

        $returnClass = IpApiResponce::class;
        $uri = sprintf($this->options['endpoint'] . '/json/%s?fields=status,country,proxy', $proxy->getIp());
        return $this->client->requestAsync(Request::METHOD_GET, $uri, $options)->then(
            function (ResponseInterface $response) use ($returnClass) {
                return $this->serializer->deserialize((string) $response->getBody(), $returnClass, 'json');
            }
        )->wait();
    }
}
