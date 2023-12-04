<?php

declare(strict_types=1);

namespace App\Converter;

use App\Dto\ErrorMessage;
use App\Exception\RestException;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DtoParametersDeserializer implements ParamConverterInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly Serializer $serializer,
    ) {
    }

    /**
     * @throws RestException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $dto = $this->releaseDto((string)$request->getContent(), $configuration);

        $request->attributes->set($configuration->getName(), $dto);

        return true;
    }

    /**
     * @throws RestException
     */
    protected function releaseDto(string $content, ParamConverter $configuration): object
    {
        $dto = $this->serializer->deserialize(
            $content,
            $configuration->getClass(),
            'json',
        );

        $violations = $this->validator->validate($dto);
        if ($violations->count() > 0) {
            $restException = new RestException();
            foreach ($violations as $violation) {
                $restException->getRestResponse()->addError(ErrorMessage::fromViolation($violation));
            }

            throw $restException;
        }

        return $dto;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'dto' === $configuration->getConverter();
    }
}
