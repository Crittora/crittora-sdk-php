<?php

namespace Crittora\Encryption;

use Crittora\Exception\CrittoraException;
use Crittora\Config\ConfigManager;
use Crittora\Http\HttpClient;

class EncryptionService
{
    private static $instance = null;
    private $baseUrl;
    private $httpClient;

    private function __construct()
    {
        $config = ConfigManager::getInstance()->getConfig();
        $this->baseUrl = $config['baseUrl'];
        $this->httpClient = HttpClient::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getHeaders(string $idToken): array
    {
        return [
            'Authorization' => "Bearer {$idToken}",
            'api_key' => getenv('API_KEY') ?: '',
            'access_key' => getenv('ACCESS_KEY') ?: '',
            'secret_key' => getenv('SECRET_KEY') ?: '',
            'Content-Type' => 'application/json'
        ];
    }

    public function encrypt(string $idToken, string $data, array $permissions = []): string
    {
        $url = $this->baseUrl . '/encrypt';
        $headers = $this->getHeaders($idToken);
        $payload = [
            'data' => $data,
            'requested_actions' => ['e']
        ];

        if (!empty($permissions)) {
            $payload['permissions'] = $permissions;
        }

        try {
            $response = $this->httpClient->post($url, $headers, $payload);
            if (!isset($response['encrypted_data'])) {
                throw new CrittoraException('Encryption failed: Unexpected response format');
            }
            return $response['encrypted_data'];
        } catch (\Exception $e) {
            throw new CrittoraException(
                'Encryption failed: ' . $e->getMessage(),
                'ENCRYPTION_ERROR',
                is_int($e->getCode()) ? $e->getCode() : null
            );
        }
    }

    public function decrypt(string $idToken, string $encryptedData, array $permissions = []): string
    {
        $url = $this->baseUrl . '/decrypt';
        $headers = $this->getHeaders($idToken);
        $payload = [
            'encrypted_data' => $encryptedData
        ];

        if (!empty($permissions)) {
            $payload['permissions'] = $permissions;
        }

        try {
            $response = $this->httpClient->post($url, $headers, $payload);
            if (!isset($response['decrypted_data'])) {
                throw new CrittoraException('Decryption failed: Unexpected response format');
            }
            return $response['decrypted_data'];
        } catch (\Exception $e) {
            throw new CrittoraException(
                'Decryption failed: ' . $e->getMessage(),
                'DECRYPTION_ERROR',
                is_int($e->getCode()) ? $e->getCode() : null
            );
        }
    }

    public function decryptVerify(string $idToken, string $encryptedData, array $permissions = []): array
    {
        $url = $this->baseUrl . '/decrypt-verify';
        $headers = $this->getHeaders($idToken);
        $payload = [
            'encrypted_data' => $encryptedData
        ];

        if (!empty($permissions)) {
            $payload['permissions'] = $permissions;
        }

        try {
            $response = $this->httpClient->post($url, $headers, $payload);
            return $response;
        } catch (\Exception $e) {
            throw new CrittoraException(
                'Decrypt-verify failed: ' . $e->getMessage(),
                'DECRYPT_VERIFY_ERROR',
                is_int($e->getCode()) ? $e->getCode() : null
            );
        }
    }
}