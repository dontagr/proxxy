<?php
declare(strict_types=1);

namespace App\Dto\Exchange;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskRequest {
    public const KEY_IP = 'ip';
    public const KEY_PORT = 'port';

    /**
     * @JMS\SerializedName("list")
     * @Assert\NotBlank(message="Прокси список пуст или не валиден")
     */
    private array $list = [];

    public function setList(string $list): CreateTaskRequest
    {
        preg_match_all('/(\d*\.\d*\.\d*\.\d*):(\d+)/', $list, $matches);
        if (empty($matches[0])) {
            return $this;
        }

        $this->list = [];
        foreach ($matches[1] as $key => $match) {
            $this->list[] = [self::KEY_IP => $match, self::KEY_PORT => (int)$matches[2][$key]];
        }

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}