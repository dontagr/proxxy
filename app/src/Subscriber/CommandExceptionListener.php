<?php

namespace App\Subscriber;

use App\Exception\Messenger\UnrecoverableMessageValidationFailedException;
use JMS\Serializer\Exception\ValidationFailedException;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Validator\ConstraintViolationInterface;

class CommandExceptionListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly SerializerInterface $serializer)
    {
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $exception = $event->getError();
        if ($exception instanceof ValidationFailedException) {
            $this->logValidationException($exception);
        }
    }

    public function onMessengerError(WorkerMessageFailedEvent $event): void
    {
        $exception = $event->getThrowable()->getPrevious();
        if ($exception instanceof ValidationFailedException) {
            $this->logValidationException($exception);
        }
    }

    private function logValidationException(ValidationFailedException $exception): void
    {
        $contextInfo = [];
        if ($exception instanceof UnrecoverableMessageValidationFailedException) {
            $contextInfo += $exception->getContextInfo();
        }

        $this->logger->warning($exception->getMessage(), $contextInfo);

        /** @var ConstraintViolationInterface $violation */
        foreach ($exception->getConstraintViolationList() as $violation) {
            $invalidValue = $violation->getInvalidValue();
            if (!is_scalar($invalidValue)) {
                $invalidValue = $this->serializer->serialize($invalidValue, 'json');
            }
            $context = array_filter(
                array_merge([
                    'propertyPath' => $violation->getPropertyPath(),
                    'value' => $invalidValue,
                ], $contextInfo)
            );
            $this->logger->warning($violation->getMessage(), $context);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::ERROR => ['onConsoleError', 0],
            WorkerMessageFailedEvent::class => ['onMessengerError', 0],
        ];
    }
}
