<?php

namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\QueryBus;
use App\Application\DTO\SuggestSubtasksRequest;
use App\Application\Query\SuggestSubtasksQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class SuggestSubtasksController extends AbstractController
{
    #[Route('/api/task/suggest-subtasks', name: 'api_task_suggest_subtasks', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] SuggestSubtasksRequest $request,
        QueryBus $queryBus
    ): JsonResponse {
        $result = $queryBus->ask(new SuggestSubtasksQuery($request->taskId));

        return $this->json($result);
    }
}
