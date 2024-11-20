<?php

namespace Crittora\Tests;

use PHPUnit\Framework\TestCase;
use Crittora\CrittoraSDK;
use Crittora\Auth\AuthenticationService;
use Crittora\Services\EncryptionService;

class CrittoraSDKTest extends TestCase
{
    private $sdk;
    private $mockAuthService;
    private $mockEncryptionService;

    protected function setUp(): void
    {
        // Mock AuthenticationService and EncryptionService
        $this->mockAuthService = $this->createMock(AuthenticationService::class);
        $this->mockEncryptionService = $this->createMock(EncryptionService::class);

        // Override singleton's instance to use mocks
        $authServiceReflection = new \ReflectionClass(AuthenticationService::class);
        $authInstanceProperty = $authServiceReflection->getProperty('instance');
        $authInstanceProperty->setAccessible(true);
        $authInstanceProperty->setValue($this->mockAuthService);

        $encryptionServiceReflection = new \ReflectionClass(EncryptionService::class);
        $encryptionInstanceProperty = $encryptionServiceReflection->getProperty('instance');
        $encryptionInstanceProperty->setAccessible(true);
        $encryptionInstanceProperty->setValue($this->mockEncryptionService);

        // Initialize the SDK
        $this->sdk = new CrittoraSDK();
    }

    public function testAuthenticateSuccess(): void
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

    public function testEncryptSuccess(): void
    {
        $mockIdToken = 'mockIdToken';
        $mockData = 'testData';
        $mockPermissions = ['read', 'write'];
        $mockEncryptedData = 'mockEncryptedData';

        $this->mockEncryptionService->expects($this->once())
            ->method('encrypt')
            ->with($mockIdToken, $mockData, $mockPermissions)
            ->willReturn($mockEncryptedData);

        $result = $this->sdk->encrypt($mockIdToken, $mockData, $mockPermissions);

        $this->assertEquals($mockEncryptedData, $result);
    }

    public function testDecryptSuccess(): void
    {
        $mockIdToken = 'mockIdToken';
        $mockEncryptedData = 'mockEncryptedData';
        $mockPermissions = ['read'];
        $mockDecryptedData = 'mockDecryptedData';

        $this->mockEncryptionService->expects($this->once())
            ->method('decrypt')
            ->with($mockIdToken, $mockEncryptedData, $mockPermissions)
            ->willReturn($mockDecryptedData);

        $result = $this->sdk->decrypt($mockIdToken, $mockEncryptedData, $mockPermissions);

        $this->assertEquals($mockDecryptedData, $result);
    }

    public function testDecryptVerifySuccess(): void
    {
        $mockIdToken = 'mockIdToken';
        $mockEncryptedData = 'mockEncryptedData';
        $mockPermissions = ['read'];
        $mockDecryptionResult = [
            'data' => 'mockDecryptedData',
            'verified' => true,
        ];

        $this->mockEncryptionService->expects($this->once())
            ->method('decryptVerify')
            ->with($mockIdToken, $mockEncryptedData, $mockPermissions)
            ->willReturn($mockDecryptionResult);

        $result = $this->sdk->decryptVerify($mockIdToken, $mockEncryptedData, $mockPermissions);

        $this->assertEquals($mockDecryptionResult, $result);
    }

    public function testAuthenticationFailure(): void
    {
        $this->mockAuthService->expects($this->once())
            ->method('authenticate')
            ->willThrowException(new \Exception('Authentication failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Authentication failed');

        $this->sdk->authenticate('invalidUser', 'invalidPassword');
    }

    public function testEncryptionFailure(): void
    {
        $this->mockEncryptionService->expects($this->once())
            ->method('encrypt')
            ->willThrowException(new \Exception('Encryption failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Encryption failed');

        $this->sdk->encrypt('mockIdToken', 'invalidData');
    }
}