<?php

use PHPUnit\Framework\TestCase;
use Crittora\Services\EncryptionService;
use Crittora\Exception\CrittoraException;
use Crittora\Http\HttpClient;

class EncryptionServiceTest extends TestCase
{
    private $encryptionService;
    private $httpClientMock;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClient::class);
        $this->encryptionService = EncryptionService::getTestInstance($this->httpClientMock);
    }

    public function testEncryptSuccess()
    {
        $idToken = 'test_id_token';
        $data = 'test_data';
        $permissions = ['read', 'write'];

        $this->httpClientMock->method('post')
            ->willReturn(['encrypted_data' => 'encrypted_test_data']);

        $result = $this->encryptionService->encrypt($idToken, $data, $permissions);

        $this->assertEquals('encrypted_test_data', $result);
    }

    public function testEncryptFailure()
    {
        $this->expectException(CrittoraException::class);
        $this->expectExceptionMessage('Encryption failed: Unexpected response format');

        $idToken = 'test_id_token';
        $data = 'test_data';

        $this->httpClientMock->method('post')
            ->willReturn([]);

        $this->encryptionService->encrypt($idToken, $data);
    }

    public function testDecryptSuccess()
    {
        $idToken = 'test_id_token';
        $encryptedData = 'encrypted_test_data';

        $this->httpClientMock->method('post')
            ->willReturn(['decrypted_data' => 'decrypted_test_data']);

        $result = $this->encryptionService->decrypt($idToken, $encryptedData);

        $this->assertEquals('decrypted_test_data', $result);
    }

    public function testDecryptFailure()
    {
        $this->expectException(CrittoraException::class);
        $this->expectExceptionMessage('Decryption failed: Unexpected response format');

        $idToken = 'test_id_token';
        $encryptedData = 'encrypted_test_data';

        $this->httpClientMock->method('post')
            ->willReturn([]);

        $this->encryptionService->decrypt($idToken, $encryptedData);
    }

    public function testDecryptVerifySuccess()
    {
        $idToken = 'test_id_token';
        $encryptedData = 'encrypted_test_data';

        $expectedResponse = ['decrypted_data' => 'decrypted_test_data', 'verified' => true];

        $this->httpClientMock->method('post')
            ->willReturn($expectedResponse);

        $result = $this->encryptionService->decryptVerify($idToken, $encryptedData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDecryptVerifyFailure()
    {
        $this->expectException(CrittoraException::class);
        $this->expectExceptionMessage('Decrypt-verify failed: Unexpected response format');

        $idToken = 'test_id_token';
        $encryptedData = 'encrypted_test_data';

        $this->httpClientMock->method('post')
            ->willThrowException(new \Exception('Unexpected response format'));

        $this->encryptionService->decryptVerify($idToken, $encryptedData);
    }
}
