<?php

namespace Crittora\Exception;

class CrittoraException extends \Exception
{
    private $errorCode;

    public function __construct(string $message, string $errorCode, int $status = 0)
    {
        parent::__construct($message, $status);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}