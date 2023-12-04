<?php

namespace App\Repository;

use App\Entity\Proxy;
use App\Entity\Task;
use App\Enums\ProxyStatus;
use App\Enums\ProxyType;
use DateTime;

class ProxyFactory
{
    public function createProxy(string $ip, int $port, Task $task): Proxy
    {
        return (new Proxy())
            ->setIp($ip)
            ->setPort($port)
            ->setTask($task)
            ->setTc(new DateTime())
            ->setType(ProxyType::NONE)
            ->setStatus(ProxyStatus::UNCHECK);
    }
}
