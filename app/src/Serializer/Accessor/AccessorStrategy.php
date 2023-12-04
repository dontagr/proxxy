<?php

namespace App\Serializer\Accessor;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\ValidationFailedException;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class AccessorStrategy implements AccessorStrategyInterface
{
    private const SUPPORT_TYPES = [
        'bool',
        'string',
        'int',
        'float',
    ];
    private DefaultAccessorStrategy $defaultAccessorStrategy;

    public function setDefaultAccessorStrategy(DefaultAccessorStrategy $defaultAccessorStrategy): void
    {
        $this->defaultAccessorStrategy = $defaultAccessorStrategy;
    }

    public function getValue(object $object, PropertyMetadata $metadata, SerializationContext $context)
    {
        return $this->defaultAccessorStrategy->getValue($object, $metadata, $context);
    }

    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void
    {
        $type = $metadata->type['name'];
        if (null === $value && in_array($type, self::SUPPORT_TYPES, true)) {
            $ref = new \ReflectionProperty($metadata->class, $metadata->name);
            if (false === $ref->getType()?->allowsNull()) {
                $list = new ConstraintViolationList();
                $list->add(
                    new ConstraintViolation(
                        'This value should not be null.',
                        null,
                        [],
                        $metadata->class,
                        $metadata->name,
                        $value
                    )
                );
                throw new ValidationFailedException($list);
            }
        }

        $this->defaultAccessorStrategy->setValue($object, $value, $metadata, $context);
    }
}
