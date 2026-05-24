<?php

namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\CommandBus;
use App\Application\DTO\CompleteTaskRequest;
use App\Application\Command\CompleteTaskCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CompleteTaskController
{
    #[Route('/api/task/complete', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CompleteTaskRequest $request,
        CommandBus $commandBus
    ): JsonResponse {
            $commandBus->dispatch(new CompleteTaskCommand($request->taskId));

            return new JsonResponse(['status' => 'completed'], 201);
        }
}
