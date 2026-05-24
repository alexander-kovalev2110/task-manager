<?php

namespace App\Application\Handler;

use App\Domain\User\Email;
use App\Application\Command\AssignTaskCommand;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
// use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AssignTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(AssignTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        $email = new Email($command->userEmail);

        $user = $this->userRepository->findByEmail( $email);  
        if (!$user) {
            throw new \DomainException('User not found');
        }

        // доменная логика
        $task->assignTo($user);
        
        $this->taskRepository->save($task);
    }
}