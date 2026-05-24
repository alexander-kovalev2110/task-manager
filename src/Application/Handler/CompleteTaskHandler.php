<?php

namespace App\Application\Handler;

use App\Application\Command\CompleteTaskCommand;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CompleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {}

    public function __invoke(CompleteTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        // Domain logic
        $task->complete();

        $this->taskRepository->save($task);
    }
}