<?php

// src/Command/StartTaskCommand.php
namespace App\Application\Command;

class StartTaskCommand
{
    public function __construct(
        public readonly int $taskId,
    ) {}
}