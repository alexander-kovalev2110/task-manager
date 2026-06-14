<?php

namespace App\Infrastructure\Bus;

final readonly class CommandBus extends AbstractBus
{
    public function dispatch(object $command): void
    {
        $start = microtime(true);

        $this->bus->dispatch($command);

        $this->log(
            $command,
            round((microtime(true) - $start) * 1000, 2)
        );
    }
}