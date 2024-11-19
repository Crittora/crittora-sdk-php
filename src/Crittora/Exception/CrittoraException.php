<?php

namespace Crittora\Exception;

class CrittoraException extends \Exception
{
    private $errorType;
    private $statusCode;

    public function __construct(
        string $message,
        string $errorType,
        ?int $statusCode = null
    ) {
        parent::__construct($message);
        $this->errorType = $errorType;
        $this->statusCode = $statusCode;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
