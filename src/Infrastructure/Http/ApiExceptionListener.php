<?php

namespace App\Infrastructure\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
final class ApiExceptionListener
{
    private const EXCEPTION_TO_STATUS_MAP = [
        \InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
        \DomainException::class => Response::HTTP_CONFLICT, 
    ];

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Unwrap Messenger exceptions
        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Internal Server Error';

        // Check if it's a known mapped exception
        foreach (self::EXCEPTION_TO_STATUS_MAP as $class => $status) {
            if ($exception instanceof $class) {
                $statusCode = $status;
                $message = $exception->getMessage();
                break;
            }
        }

        // Handle standard Symfony HTTP exceptions (like 404 Not Found)
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        }

        // Handle Validator exceptions explicitly if needed
        if ($exception instanceof ValidationFailedException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = $exception->getMessage(); // In a real app, format violations here
        }

        // Here we just return the generic message for 500, or specific for 4xx
        if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR && $_ENV['APP_ENV'] !== 'dev') {
            $message = 'Internal Server Error';
        } elseif ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
             // Show real error in dev
             $message = $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine(); 
        }

        $response = new JsonResponse([
            'error' => $message,
        ], $statusCode);

        $event->setResponse($response);
    }
}
