<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public ?string $title = null;
}