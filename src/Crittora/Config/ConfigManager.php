<?php

namespace Crittora\Config;

use Dotenv\Dotenv;

class ConfigManager
{
    private static $instance = null;
    private $config;

    private function __construct()
    {
        $this->loadEnv();

        $this->config = [
            'cognitoEndpoint' => getenv('COGNITO_ENDPOINT') ?: 'https://cognito-idp.us-east-1.amazonaws.com/',
            'baseUrl' => getenv('CRITTORA_BASE_URL') ?: 'https://api.crittoraapis.com',
            'userPoolId' => getenv('COGNITO_USER_POOL_ID') ?: 'us-east-1_Tmljk4Uiw',
            'clientId' => getenv('CRITTORA_CLIENT_ID'),
            'region' => getenv('AWS_REGION') ?: 'us-east-1',
            'accessKeyId' => getenv('CRITTORA_ACCESS_KEY'),
            'secretAccessKey' => getenv('CRITTORA_SECRET_KEY'),
        ];
    }

    private function loadEnv()
    {
        if (class_exists('Dotenv\Dotenv')) {
            $paths = [
                __DIR__ . '/../../../../.env',
                __DIR__ . '/../../../.env',
                __DIR__ . '/../../.env',
                __DIR__ . '/../.env',
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $dotenv = Dotenv::createImmutable(dirname($path));
                    $dotenv->load();
                    break;
                }
            }
        }
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