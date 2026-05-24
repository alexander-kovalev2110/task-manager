<?php

namespace App\Domain\User;

final class Email
{
    public function __construct(private string $value)
    {
        $this->value = mb_strtolower(trim($value));

        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}