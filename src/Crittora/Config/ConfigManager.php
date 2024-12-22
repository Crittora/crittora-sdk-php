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

        // Debugging: Check if environment variables are loaded
        if (!getenv('CRITTORA_CLIENT_ID')) {
            throw new \Exception('Environment variable CRITTORA_CLIENT_ID is not set.');
        }

        if (!getenv('CRITTORA_ACCESS_KEY') || !getenv('CRITTORA_SECRET_KEY')) {
            throw new \Exception('Environment variables CRITTORA_ACCESS_KEY or CRITTORA_SECRET_KEY are not set.');
        }
    }

    private function loadEnv()
    {
        // Check if Dotenv class exists (it should be a dependency of the SDK)
        if (class_exists('Dotenv\Dotenv')) {
            // Try to locate the .env file
            $paths = [
                __DIR__ . '/../../../../.env', // From SDK to project root
                __DIR__ . '/../../../.env',    // From SDK to vendor parent
                __DIR__ . '/../../.env',       // From SDK to vendor
                __DIR__ . '/../.env',          // From SDK root
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $dotenv = Dotenv::createImmutable(dirname($path));
                    $dotenv->load();
                    break;
                }
            }
        }

        // Load configuration from environment variables
        $this->config = [
            'cognitoEndpoint' => getenv('COGNITO_ENDPOINT') ?: 'https://cognito-idp.us-east-1.amazonaws.com/',
            'baseUrl' => getenv('CRITTORA_BASE_URL') ?: 'https://api.crittoraapis.com',
            'userPoolId' => getenv('COGNITO_USER_POOL_ID') ?: 'us-east-1_Tmljk4Uiw',
            'clientId' => getenv('CRITTORA_CLIENT_ID') ?: '5cvaao4qgphfp38g433vi5e82u',
            'region' => getenv('AWS_REGION') ?: 'us-east-1',
            'accessKeyId' => getenv('CRITTORA_ACCESS'),
            'secretAccessKey' => getenv('CRITTORA_SECRET_KEY'),
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