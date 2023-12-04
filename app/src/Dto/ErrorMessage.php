<?php

declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Throwable;

class ErrorMessage
{
    private string $code;
    private string $message;

    /**
     * @JMS\Groups({"debug"})
     */
    private int $line = 0;

    /**
     * @JMS\Groups({"debug"})
     */
    private string $file = '';

    /**
     * @JMS\Groups({"debug"})
     */
    private string $class = '';

    /**
     * @JMS\Groups({"debug"})
     */
    private string $trace = '';

    public function __construct(string $message, string $code = '')
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }

    public function setLine(int $line): ErrorMessage
    {
        $this->line = $line;

        return $this;
    }

    public function setFile(string $file): ErrorMessage
    {
        $this->file = $file;

        return $this;
    }

    public function setClass(string $class): ErrorMessage
    {
        $this->class = $class;

        return $this;
    }

    public function setTrace(string $trace): ErrorMessage
    {
        $this->trace = $trace;

        return $this;
    }

    public static function fromThrowable(Throwable $throwable): self
    {
        $self = new self($throwable->getMessage(), (string) $throwable->getCode());
        $self->line = $throwable->getLine();
        $self->file = $throwable->getFile();
        $self->class = get_class($throwable);
        $self->trace = $throwable->getTraceAsString();

        return $self;
    }

    public static function fromViolation(ConstraintViolationInterface $violation): self
    {
        $self = new self(
            $violation->getPropertyPath().' '.$violation->getMessage(),
            (string) $violation->getCode()
        );
        $self->class = $violation->getPropertyPath();

        return $self;
    }

    public static function create(string $string, string $code = ''): self
    {
        $self = new self($string, $code);
        $self->class = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]['class'] ?: '';

        return $self;
    }
}
