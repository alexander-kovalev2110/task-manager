<?php

namespace App\Domain\Exception;

use App\Domain\Task\TaskStatus;

class CannotCompleteTaskException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot complete task');
    }
}
