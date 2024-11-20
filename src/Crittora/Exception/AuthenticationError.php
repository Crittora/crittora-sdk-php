<?php

namespace Crittora\Exception;

class AuthenticationError extends CrittoraException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'AUTH_ERROR');
    }
} 