<?php

// src/DTO/StartTaskRequest.php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StartTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $taskId,
    ) {}
}