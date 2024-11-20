<?php

namespace Crittora\Exception;

class DecryptionError extends CrittoraException
{
    public function __construct(string $message, ?int $status = null)
    {
        parent::__construct($message, 'DECRYPTION_ERROR', $status);
    }
} 