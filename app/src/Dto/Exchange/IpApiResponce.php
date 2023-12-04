<?php
declare(strict_types=1);

namespace App\Dto\Exchange;

class IpApiResponce
{
    private bool $status = false;
    private string $country;
    private bool $proxy = false;

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status === 'success';

        return $this;
    }

    public function setStatusBool(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function isProxy(): bool
    {
        return $this->proxy;
    }

    public function setProxy(bool $proxy): self
    {
        $this->proxy = $proxy;

        return $this;
    }

    public function asArray(): array
    {
        return [
            'status' => $this->status,
            'country' => $this->getCountry(),
            'proxy' => $this->isProxy()
        ];
    }
}