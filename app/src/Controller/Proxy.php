<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\ErrorResponse;
use App\Dto\Exchange\CreateTaskResponce;
use App\Dto\Exchange\CreateTaskRequest;
use App\Repository\TaskRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'proxy', description: 'proxy handler')]
class Proxy extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'html text'
    )]
    public function index(): Response
    {
        return $this->render('proxy/index.html.twig', ['holder' => "127.0.0.1:80\n127.0.0.1:81"]);
    }

    #[Route('/task', name: 'create-task', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Create task by proxy list',
        required: true,
        attachables: [
            new Model(type: CreateTaskRequest::class, groups: ['Default', 'input']),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Resend order sms response',
        attachables: [
            new Model(type: CreateTaskResponce::class, groups: ['Default', 'output']),
        ]
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Some validation error',
        attachables: [
            new Model(type: ErrorResponse::class, groups: ['Default', 'output']),
        ]
    )]
    /**
     * @ParamConverter(name="createTaskRequest", class=CreateTaskRequest::class, converter="dto")
     */
    public function createTask(CreateTaskRequest $createTaskRequest, TaskRepository $taskRep): CreateTaskResponce
    {
        $task = $taskRep->createTask($createTaskRequest);

        return (new CreateTaskResponce())->setId($task->getId());
    }

    #[Route('/task/{taskId<[0-9]{1}[0-9]*>}', name: 'check-task', methods: ['GET'])]
    #[OA\PathParameter(
        name: 'taskId',
        description: 'id of task',
        required: true,
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'html text'
    )]
    public function checkTask(int $taskId, TaskRepository $taskRep): Response
    {
        $task = $taskRep->find($taskId);
        if (!$task) {
            return new Response("Страница не найдена",404);
        }

        return $this->render('proxy/check.html.twig', ['task' => $taskRep->find($taskId)]);
    }
}