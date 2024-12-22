<?php

namespace Crittora;

use Crittora\Auth\AuthenticationService;
use Crittora\Services\EncryptionService;
use Crittora\Config\ConfigManager; # check for env file
use Crittora\Exception\CrittoraException;

class CrittoraSDK
{
    private $authService;
    private $encryptionService;
    private $config;

    public function __construct($accessKey = null, $secretKey = null)
    {
        $this->config = ConfigManager::getInstance()->getConfig();

        if ($accessKey !== null && $secretKey !== null) {
            $this->config['accessKeyId'] = $accessKey;
            $this->config['secretAccessKey'] = $secretKey;
        }

        $this->validateConfig();

        $this->authService = AuthenticationService::getInstance();
        $this->encryptionService = EncryptionService::getInstance();
    }

    private function validateConfig()
    {
        $requiredKeys = ['clientId', 'userPoolId', 'region', 'accessKeyId', 'secretAccessKey'];
        foreach ($requiredKeys as $key) {
            if (empty($this->config[$key])) {
                throw new CrittoraException("Missing required configuration: $key", 'CONFIG_ERROR');
            }
        }
    }

    public function authenticate(string $username, string $password): array
    {
        return $this->authService->authenticate($username, $password);
    }

    public function encrypt(string $idToken, string $data, array $permissions = []): string
    {
        return $this->encryptionService->encrypt($idToken, $data, $permissions);
    }

    public function decrypt(string $idToken, string $encryptedData, array $permissions = []): string
    {
        return $this->encryptionService->decrypt($idToken, $encryptedData, $permissions);
    }

    public function decryptVerify(string $idToken, string $encryptedData, array $permissions = []): array
    {
        return $this->encryptionService->decryptVerify($idToken, $encryptedData, $permissions);
    }
}