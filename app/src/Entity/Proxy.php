<?php

namespace App\Entity;

use App\Enums\ProxyStatus;
use App\Enums\ProxyType;
use App\Repository\ProxyRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProxyRepository::class)]
class Proxy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'proxies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column]
    private ?int $port = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $tc = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(nullable: true)]
    private ?array $prop = null;

    #[ORM\Column(nullable: true)]
    private int $locking = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function getTc(): ?DateTimeInterface
    {
        return $this->tc;
    }

    public function setTc(DateTimeInterface $tc): static
    {
        $this->tc = $tc;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeText(): string
    {
        return ProxyType::getText($this->type);
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getStatusText(): ?string
    {
        return ProxyStatus::getText($this->status);
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProp(): ?array
    {
        return $this->prop;
    }

    public function setProp(?array $prop): static
    {
        $this->prop = $prop;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->prop['country'] ?? '';
    }


    public function getLocking(): ?int
    {
        return $this->locking;
    }

    public function setLocking(int $locking): static
    {
        $this->locking = $locking;

        return $this;
    }
}
