<?php

namespace Crittora;

use Crittora\Auth\AuthenticationService;
use Crittora\Services\EncryptionService;
use Crittora\Exception\CrittoraException;

class CrittoraSDK
{
    private $authService;
    private $encryptionService;

    public function __construct()
    {
        $this->authService = AuthenticationService::getInstance();
        $this->encryptionService = EncryptionService::getInstance();
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