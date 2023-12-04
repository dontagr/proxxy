<?php
declare(strict_types=1);

namespace App\Dto\Exchange;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class Test {
    /**
     * @JMS\SerializedName("name")
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @JMS\Type("string")
     */
    private string $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}