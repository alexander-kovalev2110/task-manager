<?php

// src/Handler/StartTaskHandler.php
namespace App\Application\Handler;

use App\Application\Command\StartTaskCommand;
use App\Domain\Task\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StartTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(StartTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        // Доменная логика
        $task->start();

        $this->taskRepository->save($task);
    }
}