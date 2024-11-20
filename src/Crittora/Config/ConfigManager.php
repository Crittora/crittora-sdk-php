<?php

namespace Crittora\Config;

class ConfigManager
{
    private static $instance = null;
    private $config;

    private function __construct()
    {
        // Manually load environment variables from .env file if it exists
        $envPath = __DIR__ . '/../../../.env';
        if (file_exists($envPath)) {
            $envFile = file_get_contents($envPath);
            $lines = explode("\n", $envFile);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    putenv("$key=$value");
                }
            }
        }

        // Debugging: Check if environment variables are loaded
        if (!getenv('COGNITO_CLIENT_ID')) {
            throw new \Exception('Environment variable COGNITO_CLIENT_ID is not set.');
        }

        if (!getenv('AWS_ACCESS_KEY_ID') || !getenv('AWS_SECRET_ACCESS_KEY')) {
            throw new \Exception('Environment variables AWS_ACCESS_KEY_ID or AWS_SECRET_ACCESS_KEY are not set.');
        }

        $this->config = [
            'cognitoEndpoint' => getenv('COGNITO_ENDPOINT') ?: 'https://cognito-idp.us-east-1.amazonaws.com/',
            'baseUrl' => getenv('CRITTORA_BASE_URL') ?: 'https://api.crittoraapis.com',
            'userPoolId' => getenv('COGNITO_USER_POOL_ID') ?: 'us-east-1_Tmljk4Uiw',
            'clientId' => getenv('COGNITO_CLIENT_ID') ?: '5cvaao4qgphfp38g433vi5e82u',
            'region' => getenv('AWS_REGION') ?: 'us-east-1',
            'accessKeyId' => getenv('AWS_ACCESS_KEY_ID'),
            'secretAccessKey' => getenv('AWS_SECRET_ACCESS_KEY'),
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
} 