<?php

namespace App\Application\Command;

use App\Domain\User\Email;

class LoginUserCommand
{
    public function __construct(
        public Email $email,
        public string $password
    ) {}
}