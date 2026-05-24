<?php

namespace App\Application\QueryHandler;

use App\Application\Query\GetTaskListQuery;
use App\Application\DTO\TaskView;
use App\Domain\Task\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler] 
final class GetTaskListHandler
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function __invoke(GetTaskListQuery $query): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('t', 'u')
            ->from(Task::class, 't')
            ->leftJoin('t.assignee', 'u')
            ->setMaxResults($query->limit)
            ->setFirstResult($query->offset);

        if ($query->assigneeId) {
            $qb->andWhere('u.id = :id')
               ->setParameter('id', $query->assigneeId);
        }

        $tasks = $qb->getQuery()->getResult();

        return array_map(function ($task) {
            $assignee = $task->getAssignee();

            return new TaskView(
                $task->getId(),
                $task->getTitle(),
                $assignee ? (string) $assignee->getEmail() : null,
                $task->getStatus()
            );
        }, $tasks);
    }
}