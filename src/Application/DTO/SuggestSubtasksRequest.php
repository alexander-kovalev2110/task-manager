<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SuggestSubtasksRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $taskId,
    ) {}
}
