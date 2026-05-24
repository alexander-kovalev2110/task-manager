<?php

namespace App\Infrastructure\Bus;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class QueryBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function ask(object $query): mixed
    {
        $envelope = $this->bus->dispatch($query);

        return $envelope
            ->last(HandledStamp::class)
            ?->getResult();
    }
}