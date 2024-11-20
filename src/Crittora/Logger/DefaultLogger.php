<?php

namespace Crittora\Logger;

class DefaultLogger
{
    public function info(string $message, array $context = [])
    {
        // Simple logging to a file or output
        echo "[INFO] $message " . json_encode($context) . PHP_EOL;
    }
} 