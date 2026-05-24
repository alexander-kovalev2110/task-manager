<?php

namespace App\Domain\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;

    public function findById(int $id): Task;
}