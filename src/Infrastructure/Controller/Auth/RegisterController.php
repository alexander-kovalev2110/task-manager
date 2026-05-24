<?php

namespace App\Infrastructure\Controller\Auth;

use App\Infrastructure\Bus\CommandBus;
use App\Application\Command\RegisterUserCommand;
use App\Application\DTO\RegisterUserRequest;
use App\Domain\User\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] RegisterUserRequest $request,
        CommandBus $commandBus
    ): JsonResponse {
        $commandBus->dispatch(
            new RegisterUserCommand(
                new Email($request->email),
                $request->password
            )
        );

        return $this->json(null, 201);
    }
}