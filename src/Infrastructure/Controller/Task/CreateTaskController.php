<?php

namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\CommandBus;
use App\Application\Command\CreateTaskCommand;
use App\Application\DTO\CreateTaskRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CreateTaskController extends AbstractController
{
    #[Route('/api/task', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CreateTaskRequest $request,
        CommandBus $commandBus
    ): JsonResponse {
        $commandBus->dispatch(new CreateTaskCommand($request->title));

        return $this->json(null, 201);
    }
}