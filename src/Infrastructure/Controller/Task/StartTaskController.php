<?php

namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\CommandBus;
use App\Application\DTO\StartTaskRequest;
use App\Application\Command\StartTaskCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class StartTaskController
{
    #[Route('/api/task/start', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] StartTaskRequest $request,
        CommandBus $commandBus
    ): JsonResponse {
        try {
            $commandBus->dispatch(new StartTaskCommand($request->taskId));
        } catch (HandlerFailedException $e) {
            $original = $e->getPrevious();

            if ($original instanceof \DomainException) {
                return new JsonResponse([
                    'error' => $original->getMessage()
                ], 409); // 👈 ВАЖНО
            }

            throw $e;
        }

        return new JsonResponse(['status' => 'started']);
    }
}