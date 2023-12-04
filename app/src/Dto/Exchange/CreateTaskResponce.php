<?php
declare(strict_types=1);

namespace App\Dto\Exchange;

class CreateTaskResponce {
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}