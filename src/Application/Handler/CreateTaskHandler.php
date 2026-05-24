<?php

namespace App\Application\Handler;

use App\Application\Command\CreateTaskCommand;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Task\Task;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTaskHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TaskRepositoryInterface $taskRepository,
    ) {}

    public function __invoke(CreateTaskCommand $command): void
    {
        // Create task 
        $task = new Task($command->title);

        $this->taskRepository->save($task);
    }
}