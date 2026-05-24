<?php

namespace App\Application\Command;

final class CreateTaskCommand
{
    public function __construct(
        public readonly string $title,
    ) {}
}

