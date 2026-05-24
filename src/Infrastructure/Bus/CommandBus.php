<?php

namespace App\Infrastructure\Bus;

use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function dispatch(object $command): void
    {
        $this->bus->dispatch($command);
    }
}