<?php

namespace Crittora\Exception;

class EncryptionError extends CrittoraException
{
    public function __construct(string $message, ?int $status = null)
    {
        parent::__construct($message, 'ENCRYPTION_ERROR', $status);
    }
} 