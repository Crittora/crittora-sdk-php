<?php

namespace Crittora\Auth;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;
use Crittora\Config\ConfigManager;
use Exception;

class AuthenticationService
{
    private static $instance = null;
    private $client;
    private $config;

    private function __construct()
    {
        $this->config = ConfigManager::getInstance()->getConfig();
        $this->client = new CognitoIdentityProviderClient([
            'version' => 'latest',
            'region'  => $this->config['region']
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function authenticate(string $username, string $password): array
    {
        error_log("Authenticating user with username: $username");

        try {
            $result = $this->client->initiateAuth([
                'AuthFlow' => 'USER_PASSWORD_AUTH',
                'ClientId' => $this->config['clientId'],
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password
                ],
            ]);

            error_log("Authentication successful for user: $username");
            error_log("IdToken: " . $result['AuthenticationResult']['IdToken']);
            error_log("AccessToken: " . $result['AuthenticationResult']['AccessToken']);
            error_log("RefreshToken: " . $result['AuthenticationResult']['RefreshToken']);

            return [
                'IdToken' => $result['AuthenticationResult']['IdToken'],
                'AccessToken' => $result['AuthenticationResult']['AccessToken'],
                'RefreshToken' => $result['AuthenticationResult']['RefreshToken'],
            ];
        } catch (AwsException $e) {
            error_log("Authentication failed for user: $username with error: " . $e->getMessage());
            throw new Exception('Authentication failed: ' . $e->getMessage());
        }
    }

    public static function getTestInstance($mockClient): self
    {
        $instance = new self();
        $instance->client = $mockClient;
        return $instance;
    }

    public function refreshTokens(string $refreshToken): array
    {
        try {
            $result = $this->client->initiateAuth([
                'AuthFlow' => 'REFRESH_TOKEN_AUTH',
                'ClientId' => $this->config['clientId'],
                'AuthParameters' => [
                    'REFRESH_TOKEN' => $refreshToken,
                ],
            ]);

            return [
                'IdToken' => $result['AuthenticationResult']['IdToken'],
                'AccessToken' => $result['AuthenticationResult']['AccessToken'],
            ];
        } catch (AwsException $e) {
            throw new Exception('Token refresh failed: ' . $e->getMessage());
        }
    }
}
