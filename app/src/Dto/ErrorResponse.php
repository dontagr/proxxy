<?php

declare(strict_types=1);

namespace App\Dto;

use Countable;

class ErrorResponse implements Countable
{
    public array $errors = [];

    public function count(): int
    {
        return count($this->errors);
    }

    public function pushError(ErrorMessage $errorMessage): self
    {
        $this->errors[] = $errorMessage;

        return $this;
    }
}
