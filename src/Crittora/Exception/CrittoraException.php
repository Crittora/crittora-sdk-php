<?php

namespace Crittora\Exception;

class CrittoraException extends \Exception
{
    public function __construct(string $message, string $code = '', int $status = 0)
    {
        parent::__construct($message, $status);
        $this->code = $code;
    }
}