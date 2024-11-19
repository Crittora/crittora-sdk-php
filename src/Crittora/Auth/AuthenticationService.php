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
    private $srpA;
    private $a;
    private $N;
    private $g;
    private $k;

    private function __construct()
    {
        $this->config = ConfigManager::getInstance()->getConfig();
        
        $this->client = new CognitoIdentityProviderClient([
            'version' => 'latest',
            'region'  => $this->config['region'],
            'credentials' => [
                'key'    => $this->config['accessKeyId'],
                'secret' => $this->config['secretAccessKey'],
            ],
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Authenticates a user with their username and password
     * 
     * @param string $username
     * @param string $password
     * @return array Authentication result containing tokens
     * @throws Exception
     */
    public function authenticate(string $username, string $password): array
    {
        try {
            $result = $this->client->initiateAuth([
                'AuthFlow' => 'USER_SRP_AUTH',
                'ClientId' => $this->config['clientId'],
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'SRP_A' => $this->calculateSRPA($password)
                ],
            ]);

            // Handle SRP challenge
            if (isset($result['ChallengeName']) && $result['ChallengeName'] === 'PASSWORD_VERIFIER') {
                $challengeResponse = $this->respondToSRPChallenge(
                    $result['ChallengeParameters'],
                    $username,
                    $password
                );

                return [
                    'IdToken' => $challengeResponse['AuthenticationResult']['IdToken'],
                    'AccessToken' => $challengeResponse['AuthenticationResult']['AccessToken'],
                    'RefreshToken' => $challengeResponse['AuthenticationResult']['RefreshToken'],
                ];
            }

            throw new Exception('Unexpected authentication response');
        } catch (AwsException $e) {
            throw new Exception('Authentication failed: ' . $e->getMessage());
        }
    }

    private function calculateSRPA(string $password): string
    {
        // Initialize constants
        $this->N = gmp_init('FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AAAC42DAD33170D04507A33A85521ABDF1CBA64ECFB850458DBEF0A8AEA71575D060C7DB3970F85A6E1E4C7ABF5AE8CDB0933D71E8C94E04A25619DCEE3D2261AD2EE6BF12FFA06D98A0864D87602733EC86A64521F2B18177B200CBBE117577A615D6C770988C0BAD946E208E24FA074E5AB3143DB5BFCE0FD108E4B82D120A93AD2CAFFFFFFFFFFFFFFFF', 16);
        $this->g = gmp_init('2');
        $this->k = gmp_init('3');

        // Generate random 'a' value
        $this->a = gmp_random_bits(256);
        
        // Calculate A = g^a % N
        $this->srpA = gmp_powm($this->g, $this->a, $this->N);
        
        return gmp_strval($this->srpA, 16);
    }

    private function respondToSRPChallenge(array $challengeParams, string $username, string $password): array
    {
        $timestamp = gmdate('D M d H:i:s T Y');
        
        // Extract challenge parameters
        $srpB = gmp_init($challengeParams['SRP_B'], 16);
        $salt = hex2bin($challengeParams['SALT']);
        $secretBlock = $challengeParams['SECRET_BLOCK'];
        
        // Calculate u = H(A || B)
        $u = gmp_init(hash('sha256', gmp_strval($this->srpA, 16) . $challengeParams['SRP_B']), 16);
        
        // Calculate x = H(salt || H(username || ':' || password))
        $x = gmp_init(hash('sha256', $salt . hash('sha256', $username . ':' . $password, true)), 16);
        
        // Calculate S = (B - k * g^x)^(a + u * x) % N
        $kgx = gmp_mul($this->k, gmp_powm($this->g, $x, $this->N));
        $aux = gmp_add($this->a, gmp_mul($u, $x));
        $S = gmp_powm(gmp_sub($srpB, $kgx), $aux, $this->N);
        
        // Calculate K = H(S)
        $K = hash('sha256', gmp_strval($S, 16));
        
        // Calculate signature
        $message = hash('sha256', 
            implode('', [
                $challengeParams['USER_POOL_ID'],
                $challengeParams['USER_ID_FOR_SRP'],
                $secretBlock,
                $timestamp
            ])
        );
        
        $signature = base64_encode(
            hash_hmac('sha256', $message, hex2bin($K), true)
        );

        return $this->client->respondToAuthChallenge([
            'ChallengeName' => 'PASSWORD_VERIFIER',
            'ClientId' => $this->config['clientId'],
            'ChallengeResponses' => [
                'USERNAME' => $username,
                'TIMESTAMP' => $timestamp,
                'PASSWORD_CLAIM_SECRET_BLOCK' => $secretBlock,
                'PASSWORD_CLAIM_SIGNATURE' => $signature
            ]
        ]);
    }

    /**
     * Refreshes the authentication tokens using a refresh token
     * 
     * @param string $refreshToken
     * @return array New authentication tokens
     * @throws Exception
     */
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
