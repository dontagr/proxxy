<?php

declare(strict_types=1);

namespace App\Exception\Messenger;

use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Exception\UnrecoverableExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UnrecoverableMessageValidationFailedException extends ValidationFailedException implements UnrecoverableExceptionInterface
{
    public function __construct(ConstraintViolationListInterface $list, private readonly array $contextInfo = [])
    {
        parent::__construct($list);
    }

    public function getContextInfo(): array
    {
        return $this->contextInfo;
    }
}
