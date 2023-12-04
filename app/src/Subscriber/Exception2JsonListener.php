<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Dto\ErrorMessage;
use App\Dto\RestResponse;
use App\Exception\Exchange\ExchangeExceptionInterface;
use App\Exception\RestException;
use Doctrine\ORM\EntityNotFoundException;
use JMS\Serializer\Exception\Exception as SerializerExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class Exception2JsonListener implements EventSubscriberInterface
{
    use ResponseListenerTrait;

    /**
     * Serialize entity and initializes a new response object with the json content.
     */
    public function onKernelView(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $response = RestResponse::new(Response::HTTP_INTERNAL_SERVER_ERROR);

        switch (true) {
            case $throwable instanceof AccessDeniedHttpException:
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                break;
            case $throwable instanceof HttpExceptionInterface:
                $response->setStatusCode($throwable->getStatusCode());
                break;
            case $throwable instanceof SerializerExceptionInterface:
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                break;
            case $throwable instanceof EntityNotFoundException:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                break;
            case $throwable instanceof RestException:
                $response = $throwable->getRestResponse();
                break;
                // app exceptions
            case $throwable instanceof ExchangeExceptionInterface:
                $response->setStatusCode($throwable->getCode())->addError(ErrorMessage::fromThrowable($throwable));
                break;
        }

        if (0 === $response->getErrorResponse()->count()) {
            do {
                $response->addError(ErrorMessage::fromThrowable($throwable));
            } while ('prod' !== $this->kernelEnvironment && ($throwable = $throwable->getPrevious()));
        }

        $event->setResponse($response->setJson($this->serialize($response->getErrorResponse())));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelView', 0],
            ],
        ];
    }
}
