<?php

namespace Crittora\Exception;

class CrittoraException extends \Exception
{
    public function __construct(string $message, string $code, ?int $status = null)
    {
        parent::__construct($message, $status);
        // Additional initialization if needed
    }
}
