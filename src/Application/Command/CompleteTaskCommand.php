<?php

// src/Command/CompleteTaskCommand.php
namespace App\Application\Command;

class CompleteTaskCommand
{
    public function __construct(
        public readonly int $taskId,
    ) {}
}