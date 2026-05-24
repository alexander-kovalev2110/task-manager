<?php

namespace App\Application\Command;

use App\Domain\User\Email;

class RegisterUserCommand
{
    public function __construct(
        public Email $email,
        public string $password
    ) {}
}