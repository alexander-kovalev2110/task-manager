<?php

namespace App\Infrastructure\Repository;

use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\Email;
use App\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function getById(int $id): User
    {
        $user = $this->em->find(User::class, $id);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        return $user;
    }

    public function findByEmail(Email $email): ?User 
    {
        return $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    public function existsByEmail(Email $email): bool
    {
        return null !== $this->findByEmail($email);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}