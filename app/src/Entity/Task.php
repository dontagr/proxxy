<?php

namespace App\Entity;

use App\Enums\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /** @JMS\Groups({"create"}) */
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Proxy::class)]
    private Collection $proxies;

    public function __construct()
    {
        $this->proxies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Proxy>
     */
    public function getProxies(): Collection
    {
        return $this->proxies;
    }

    public function addProxy(Proxy $proxy): static
    {
        if (!$this->proxies->contains($proxy)) {
            $this->proxies->add($proxy);
            $proxy->setTask($this);
        }

        return $this;
    }

    public function removeProxy(Proxy $proxy): static
    {
        // set the owning side to null (unless already changed)
        if ($this->proxies->removeElement($proxy) && $proxy->getTask() === $this) {
            $proxy->setTask(null);
        }

        return $this;
    }
}
