<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Dto\ErrorMessage;
use App\Dto\RestResponse;
use App\Logger\RequestIdProcessor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ResponseListenerTrait
{
    public function __construct(
        private readonly Serializer         $serializer,
        private readonly RequestIdProcessor $requestIdProcessor,
        private readonly string             $kernelEnvironment,
    ) {
    }

    private function violationListToResponse(ConstraintViolationListInterface $list): Response
    {
        $response = RestResponse::new(Response::HTTP_NO_CONTENT);
        if ($list->count() > 0) {
            $response = RestResponse::new(Response::HTTP_BAD_REQUEST);
        }

        foreach ($list as $violation) {
            $response->addError(ErrorMessage::fromViolation($violation));
        }

        return $response;
    }

    private function serialize($result): string
    {
        $contextGroups[] = 'Default'; // JMS Serializer "Default"
        $contextGroups[] = 'output';

        $context = SerializationContext::create();
        if (preg_match('~^(dev|test)$~ui', $this->kernelEnvironment)) {
            $contextGroups[] = 'debug';
        }

        $context->setGroups($contextGroups);

        return $this->serializer->serialize($result, 'json', $context);
    }
}
