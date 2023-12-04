<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\HttpFoundation\JsonResponse;

class RestResponse extends JsonResponse
{
    private ErrorResponse $errorResponse;
    private mixed $payload = null;

    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        $this->errorResponse = new ErrorResponse();

        parent::__construct($data, $status, $headers, $json);
    }

    public function getErrorResponse(): ErrorResponse
    {
        return $this->errorResponse;
    }

    public function addError(ErrorMessage $error): self
    {
        $this->errorResponse->pushError($error);

        return $this;
    }

    public function addNewError(string $message, string $code = ''): self
    {
        $this->errorResponse->pushError(ErrorMessage::create($message, $code));

        return $this;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function setPayload(mixed $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public static function new($status = 200, $payload = null, array $headers = []): self
    {
        return (new self(null, $status, $headers))->setPayload($payload);
    }
}
