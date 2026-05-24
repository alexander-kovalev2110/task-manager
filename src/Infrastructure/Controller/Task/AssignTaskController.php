<?php

// src/Controller/Task/AssignTaskController.php

namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\CommandBus;
use App\Application\DTO\AssignTaskRequest;
use App\Application\Command\AssignTaskCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AssignTaskController extends AbstractController
{
    #[Route('/api/task/assignee', methods: ['PUT'])]
    public function __invoke(
        #[MapRequestPayload] AssignTaskRequest $request,
        CommandBus $commandBus
    ): JsonResponse {
        $commandBus->dispatch(new AssignTaskCommand(
            $request->taskId,
            $request->userEmail
        ));

        return $this->json(null, 201);
    }
}