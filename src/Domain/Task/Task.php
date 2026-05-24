<?php

namespace App\Domain\Task;

use App\Repository\DoctrineTaskRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Task\TaskStatus;
use App\Domain\User\User;

#[ORM\Entity(repositoryClass: DoctrineTaskRepository::class)]
class Task
{
    private function validateTitle(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        return $title;
    }

    public function __construct(string $title)
    {
        $this->title = $this->validateTitle($title);
        $this->status = TaskStatus::TODO;
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(enumType: TaskStatus::class)]
    private TaskStatus $status;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $assignee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function rename(string $title): void
    {
        $this->title = $this->validateTitle($title);
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function start(): void
    {
        if ($this->assignee === null) {
            throw new \DomainException('Task cannot be started without an assignee');
        }
        $this->status = $this->status->start();
    }

    public function complete(): void
    {
        $this->status = $this->status->complete();
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function assignTo(?User $user): void
    {
        if ($this->status === TaskStatus::DONE) {
            throw new \DomainException('Cannot assign completed task');
        }

        if ($this->assignee === $user) {
            return;
        }

        // Remove from the old user
        if ($this->assignee !== null) {
            $this->assignee->getTasks()->removeElement($this);
        }

        $this->assignee = $user;

        // 
        if ($user !== null && !$user->getTasks()->contains($this)) {
            $user->getTasks()->add($this);
        }
    }
}
