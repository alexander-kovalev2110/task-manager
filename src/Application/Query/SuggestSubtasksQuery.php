<?php

namespace App\Application\Query;

final class SuggestSubtasksQuery
{
    public function __construct(
        public readonly int $taskId,
    ) {}
}
