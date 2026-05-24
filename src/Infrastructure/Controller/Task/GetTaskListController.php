<?php

// src/Controller/Task/GetTaskListController.php
namespace App\Infrastructure\Controller\Task;

use App\Infrastructure\Bus\QueryBus;
use App\Application\Query\GetTaskListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GetTaskListController extends AbstractController
{
    #[Route('/api/tasks', methods: ['GET'])]
    public function __invoke(
        Request $request,
        QueryBus $queryBus
    ): JsonResponse {
        $query = new GetTaskListQuery(
            assigneeId: $request->query->getInt('assigneeId') ?: null,
            limit: $request->query->getInt('limit', 20),
            offset: $request->query->getInt('offset', 0),
        );

        $result = $queryBus->ask($query);

        return $this->json($result);
    }
}