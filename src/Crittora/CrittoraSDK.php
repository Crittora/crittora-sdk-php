<?php

namespace Crittora;

use Crittora\Auth\AuthenticationService;
use Crittora\Encryption\EncryptionService;
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

    /**
     * Authenticate a user with username and password
     *
     * @param string $username
     * @param string $password
     * @return array Authentication result containing tokens
     * @throws CrittoraException
     */
    public function authenticate(string $username, string $password): array
    {
        return $this->authService->authenticate($username, $password);
    }

    /**
     * Encrypt data using the Crittora API
     *
     * @param string $idToken Authentication token
     * @param string $data Data to encrypt
     * @param array $permissions Optional permissions
     * @return string Encrypted data
     * @throws CrittoraException
     */
    public function encrypt(string $idToken, string $data, array $permissions = []): string
    {
        return $this->encryptionService->encrypt($idToken, $data, $permissions);
    }

    /**
     * Decrypt data using the Crittora API
     *
     * @param string $idToken Authentication token
     * @param string $encryptedData Data to decrypt
     * @param array $permissions Optional permissions
     * @return string Decrypted data
     * @throws CrittoraException
     */
    public function decrypt(string $idToken, string $encryptedData, array $permissions = []): string
    {
        return $this->encryptionService->decrypt($idToken, $encryptedData, $permissions);
    }

    /**
     * Decrypt and verify data using the Crittora API
     *
     * @param string $idToken Authentication token
     * @param string $encryptedData Data to decrypt and verify
     * @param array $permissions Optional permissions
     * @return array Decryption and verification result
     * @throws CrittoraException
     */
    public function decryptVerify(string $idToken, string $encryptedData, array $permissions = []): array
    {
        return $this->encryptionService->decryptVerify($idToken, $encryptedData, $permissions);
    }
}