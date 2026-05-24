<?php

namespace App\Application\Handler;

use App\Application\Command\RegisterUserCommand;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Domain\Exception\UserAlreadyExistsException;

#[AsMessageHandler]
final class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(RegisterUserCommand $command): void
    {
        if ($this->userRepository->existsByEmail($command->email)) {
            throw new UserAlreadyExistsException();
        }

        $user = new User($command->email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $command->password
        );

        $user->changePassword($hashedPassword);

        $this->userRepository->save($user);
    }
}