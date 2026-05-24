<?php

namespace App\Domain\Exception;

class UserAlreadyExistsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('User already exists');
    }
}