<?php

// src/Handler/CompleteTaskHandler.php
namespace App\Application\Handler;

use App\Application\Command\CompleteTaskCommand;
use App\Domain\Task\TaskRepositoryInterface;
// use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CompleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        // private EntityManagerInterface $em,
    ) {}

    public function __invoke(CompleteTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        // 🔥 доменная логика
        $task->complete();

        // $this->em->flush();
        $this->taskRepository->save($task);
    }
}