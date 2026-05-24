<?php

namespace App\Infrastructure\Controller\Auth;

use App\Infrastructure\Bus\QueryBus;
use App\Application\Command\LoginUserCommand;
use App\Application\DTO\LoginUserRequest;
use App\Domain\User\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] LoginUserRequest $request,
        QueryBus $queryBus
    ): JsonResponse {
        $token = $queryBus->ask(
            new LoginUserCommand(
                new Email($request->email),
                $request->password
            )
        );

        return $this->json([
            'token' => $token
        ]);
    }
}