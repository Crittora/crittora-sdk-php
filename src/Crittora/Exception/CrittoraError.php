<?php

namespace Crittora\Exception;

class CrittoraError extends \Exception
{
    private $code;
    private $status;

    public function __construct(string $message, string $code, ?int $status = null)
    {
        parent::__construct($message);
        $this->code = $code;
        $this->status = $status;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
} 