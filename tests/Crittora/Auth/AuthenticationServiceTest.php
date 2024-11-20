<?php

use PHPUnit\Framework\TestCase;
use Crittora\Auth\AuthenticationService;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Result;
use Aws\Exception\AwsException;
use Aws\CommandInterface;

class AuthenticationServiceTest extends TestCase
{
    private $authService;
    private $mockClient;

    protected function setUp(): void
    {
        // Create a mock for the CognitoIdentityProviderClient
        $this->mockClient = $this->getMockBuilder(CognitoIdentityProviderClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['initiateAuth'])
            ->getMock();

        // Use the mock client in the AuthenticationService
        $this->authService = AuthenticationService::getTestInstance($this->mockClient);
    }

    public function testRefreshTokensSuccess()
    {
        // Mock a successful token refresh response
        $mockResult = new Result([
            'AuthenticationResult' => [
                'IdToken' => 'newMockIdToken',
                'AccessToken' => 'newMockAccessToken',
            ],
        ]);

        $this->mockClient->expects($this->once())
            ->method('initiateAuth')
            ->willReturn($mockResult);

        $result = $this->authService->refreshTokens('mockRefreshToken');

        $this->assertArrayHasKey('IdToken', $result);
        $this->assertArrayHasKey('AccessToken', $result);
        $this->assertEquals('newMockAccessToken', $result['AccessToken']);
    }

    public function testRefreshTokensFailure()
    {
        // Create a mock for the CommandInterface
        $mockCommand = $this->createMock(CommandInterface::class);

        // Mock a token refresh failure
        $this->mockClient->expects($this->once())
            ->method('initiateAuth')
            ->willThrowException(new AwsException(
                'Token refresh failed',
                $mockCommand,
                ['message' => 'Invalid refresh token.']
            ));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Token refresh failed');

        $this->authService->refreshTokens('invalidMockRefreshToken');
    }
}