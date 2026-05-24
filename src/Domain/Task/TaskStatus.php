<?php

namespace App\Domain\Task;

use App\Domain\Exception\CannotStartTaskException;
use App\Domain\Exception\CannotCompleteTaskException;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function start(): self
    {
        return match ($this) {
            self::TODO => self::IN_PROGRESS,
            default => throw new CannotStartTaskException($this),
        };
    }

    public function complete(): self
    {
        return match ($this) {
            self::IN_PROGRESS => self::DONE,
            default => throw new CannotCompleteTaskException($this),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }
}