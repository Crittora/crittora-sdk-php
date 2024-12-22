<?php

namespace Crittora\Services;

use Crittora\Exception\CrittoraException;
use Crittora\Config\ConfigManager;
use Crittora\Http\HttpClient;

class EncryptionService
{
    private static $instance = null;
    private $baseUrl;
    private $httpClient;

    private function __construct($httpClient = null)
    {
        $config = ConfigManager::getInstance()->getConfig();
        $this->baseUrl = $config['baseUrl'];
        $this->httpClient = $httpClient ?: HttpClient::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getTestInstance($httpClient): self
    {
        return new self($httpClient);
    }

    private function getHeaders(string $idToken): array
    {
        $apiKey = getenv('CRITTORA_API_KEY');    
        $accessKey = getenv('CRITTORA_ACCESS_KEY');
        $secretKey = getenv('CRITTORA_SECRET_KEY');

        error_log("Crittora API Key: " . $apiKey);
        error_log("Crittora Access Key: " . $accessKey);
        error_log("Crittora Secret Key: " . $secretKey);

        return [
            'Authorization' => "Bearer {$idToken}",
            'api_key' => $apiKey,
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
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
                throw new CrittoraException(
                    'Encryption failed: Unexpected response format',
                    'ENCRYPTION_ERROR'
                );
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
                throw new CrittoraException(
                    'Decryption failed: Unexpected response format',
                    'DECRYPTION_ERROR'
                );
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
            if (isset($response['body'])) {
                return json_decode($response['body'], true);
            } else {
                throw new CrittoraException(
                    'Decrypt-verify failed: Unexpected response format',
                    'DECRYPT_VERIFY_ERROR'
                );
            }
        } catch (\Exception $e) {
            throw new CrittoraException(
                'Decrypt-verify failed: ' . $e->getMessage(),
                'DECRYPT_VERIFY_ERROR',
                is_int($e->getCode()) ? $e->getCode() : null
            );
        }
    }

    public function signEncrypt(string $idToken, string $data, array $permissions = []): string
    {
        $url = $this->baseUrl . '/sign-encrypt';
        $headers = $this->getHeaders($idToken);
        $payload = [
            'data' => $data,
            'requested_actions' => ['e', 's']
        ];

        if (!empty($permissions)) {
            $payload['permissions'] = $permissions;
        }

        try {
            $response = $this->httpClient->post($url, $headers, $payload);
            if (isset($response['body'])) {
                $body = json_decode($response['body'], true);
                return $body['encrypted_data'] ?? 'An error has occurred, please check your credentials and try again.';
            } else {
                return 'An error has occurred, please check your credentials and try again.';
            }
        } catch (\Exception $e) {
            throw new CrittoraException(
                'Sign-encrypt failed: ' . $e->getMessage(),
                'SIGN_ENCRYPT_ERROR',
                is_int($e->getCode()) ? $e->getCode() : null
            );
        }
    }
}