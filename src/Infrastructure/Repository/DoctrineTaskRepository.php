<?php

namespace App\Infrastructure\Repository;

use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\Task;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function save(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function findById(int $id): Task
    {
        $task = $this->em->find(Task::class, $id);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        return $task;
    }
}