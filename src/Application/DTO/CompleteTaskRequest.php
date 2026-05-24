<?php

// src/DTO/CompleteTaskRequest.php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CompleteTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $taskId,
    ) {}
}