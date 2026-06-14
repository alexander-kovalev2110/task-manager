<?php

namespace App\Domain\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Task\Task;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'assignee')]
    private Collection $tasks;

    public function __construct(string $email)
    {
        $email = trim($email);

        if ($email === '') {
            throw new \InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $this->email = $email;
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // VO can be returned to the outside
    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function getEmailValue(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email): void
    {
        $email = trim($email);

        if ($email === '') {
            throw new \InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function changePassword(string $hashedPassword): void
    {
        if ($hashedPassword === '') {
            throw new \InvalidArgumentException('Password cannot be empty');
        }

        $this->password = $hashedPassword;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): void
    {
        $task->setAssignee($this);
    }

    public function removeTask(Task $task): void
    {
        if ($task->getAssignee() === $this) {
            $task->setAssignee(null);
        }
    }
}