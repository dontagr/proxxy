<?php

namespace App\Repository;

use App\Dto\Exchange\CreateTaskRequest;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, readonly ProxyFactory $proxyFactory)
    {
        parent::__construct($registry, Task::class);
    }

    public function createTask(CreateTaskRequest $createTaskRequest): Task
    {
        $em = $this->getEntityManager();
        $task = new Task();
        $em->persist($task);

        foreach ($createTaskRequest->getList() as $proxy) {
            $proxy = $this->proxyFactory->createProxy(
                $proxy[CreateTaskRequest::KEY_IP],
                $proxy[CreateTaskRequest::KEY_PORT],
                $task
            );
            $task->addProxy($proxy);
            $em->persist($proxy);
        }
        $em->flush();

        return $task;
    }
}
