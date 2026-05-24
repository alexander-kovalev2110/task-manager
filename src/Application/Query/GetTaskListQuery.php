<?php

namespace App\Application\Query;

final class GetTaskListQuery
{
    public function __construct(
        public readonly ?int $assigneeId = null,
        public readonly int $limit = 20,
        public readonly int $offset = 0
    ) {}
}