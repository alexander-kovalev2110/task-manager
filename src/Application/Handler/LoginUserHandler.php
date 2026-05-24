<?php

namespace App\Application\Handler;

use App\Application\Command\LoginUserCommand;
use App\Domain\User\UserRepositoryInterface;
use App\Application\Service\TokenService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LoginUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository, 
        private UserPasswordHasherInterface $hasher,
        private TokenService $tokenService
    ) {}

    public function __invoke(LoginUserCommand $command): string
    {
        dump('Handler works');
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user) {
            throw new \DomainException('Invalid credentials');
        }

        if (!$this->hasher->isPasswordValid($user, $command->password)) {
            throw new \DomainException('Invalid credentials');
        }

        return $this->tokenService->createToken($user);
    }
}