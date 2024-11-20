<?php

use PHPUnit\Framework\TestCase;
use Crittora\CrittoraSDK;
use Crittora\Auth\AuthenticationService;
use Crittora\Encryption\EncryptionService;

class CrittoraSDKTest extends TestCase
{
    private $sdk;
    private $mockAuthService;
    private $mockEncryptionService;

    protected function setUp(): void
    {
        // Create mocks for AuthenticationService and EncryptionService
        $this->mockAuthService = $this->createMock(AuthenticationService::class);
        $this->mockEncryptionService = $this->createMock(EncryptionService::class);

        // Mock the singleton pattern's getInstance method
        $authenticationServiceReflection = new ReflectionClass(AuthenticationService::class);
        $encryptionServiceReflection = new ReflectionClass(EncryptionService::class);

        $authInstanceProperty = $authenticationServiceReflection->getProperty('instance');
        $authInstanceProperty->setAccessible(true);
        $authInstanceProperty->setValue(null, $this->mockAuthService); // Pass null for static properties

        $encryptionInstanceProperty = $encryptionServiceReflection->getProperty('instance');
        $encryptionInstanceProperty->setAccessible(true);
        $encryptionInstanceProperty->setValue(null, $this->mockEncryptionService); // Pass null for static properties

        // Instantiate the SDK
        $this->sdk = new CrittoraSDK();
    }

    public function testAuthenticateSuccess()
    {
        $mockUsername = 'testUser';
        $mockPassword = 'testPassword';
        $mockResult = [
            'IdToken' => 'mockIdToken',
            'AccessToken' => 'mockAccessToken',
            'RefreshToken' => 'mockRefreshToken',
        ];

        $this->mockAuthService->expects($this->once())
            ->method('authenticate')
            ->with($mockUsername, $mockPassword)
            ->willReturn($mockResult);

        $result = $this->sdk->authenticate($mockUsername, $mockPassword);

        $this->assertEquals($mockResult, $result);
    }

    public function testEncryptSuccess()
    {
        $mockIdToken = 'mockIdToken';
        $mockData = 'testData';
        $mockPermissions = ['read', 'write'];
        $mockEncryptedData = 'encryptedTestData';

        $this->mockEncryptionService->expects($this->once())
            ->method('encrypt')
            ->with($mockIdToken, $mockData, $mockPermissions)
            ->willReturn($mockEncryptedData);

        $result = $this->sdk->encrypt($mockIdToken, $mockData, $mockPermissions);

        $this->assertEquals($mockEncryptedData, $result);
    }

    public function testDecryptSuccess()
    {
        $mockIdToken = 'mockIdToken';
        $mockEncryptedData = 'encryptedTestData';
        $mockPermissions = ['read'];
        $mockDecryptedData = 'decryptedTestData';

        $this->mockEncryptionService->expects($this->once())
            ->method('decrypt')
            ->with($mockIdToken, $mockEncryptedData, $mockPermissions)
            ->willReturn($mockDecryptedData);

        $result = $this->sdk->decrypt($mockIdToken, $mockEncryptedData, $mockPermissions);

        $this->assertEquals($mockDecryptedData, $result);
    }

    public function testDecryptVerifySuccess()
    {
        $mockIdToken = 'mockIdToken';
        $mockEncryptedData = 'encryptedTestData';
        $mockPermissions = ['read'];
        $mockDecryptionResult = [
            'data' => 'decryptedTestData',
            'verified' => true,
        ];

        $this->mockEncryptionService->expects($this->once())
            ->method('decryptVerify')
            ->with($mockIdToken, $mockEncryptedData, $mockPermissions)
            ->willReturn($mockDecryptionResult);

        $result = $this->sdk->decryptVerify($mockIdToken, $mockEncryptedData, $mockPermissions);

        $this->assertEquals($mockDecryptionResult, $result);
    }
}