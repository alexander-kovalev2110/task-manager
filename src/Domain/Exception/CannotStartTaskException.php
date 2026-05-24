<?php

namespace App\Domain\Exception;

use App\Domain\Task\TaskStatus;

class CannotStartTaskException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Task already started');
    }
}
