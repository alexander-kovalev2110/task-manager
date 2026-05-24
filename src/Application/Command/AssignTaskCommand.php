<?php

// src/Command/AssignTaskCommand.php
namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class AssignTaskCommand
{
    public function __construct(
        public readonly int $taskId,
        public readonly string $userEmail,
    ) {}
}