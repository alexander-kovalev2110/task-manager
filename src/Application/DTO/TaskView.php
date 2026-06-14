<?php

namespace App\Application\DTO;

final class TaskView
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $assigneeEmail,
        public \App\Domain\Task\TaskStatus $status,

    ) {}
}