<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AssignTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $taskId,

        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $userEmail,
    ) {}
}