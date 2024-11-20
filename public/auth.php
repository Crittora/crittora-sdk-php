<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Crittora\CrittoraSDK;
use Dotenv\Dotenv;

session_start();

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Get credentials from environment variables
    $username = $_ENV['CRITTORA_USERNAME'] ?? null;
    $password = $_ENV['CRITTORA_PASSWORD'] ?? null;

    if (!$username || !$password) {
        throw new Exception('Environment variables CRITTORA_USERNAME and CRITTORA_PASSWORD must be set');
    }

    $sdk = new CrittoraSDK();
    $result = $sdk->authenticate($username, $password);
    
    $_SESSION['auth_token'] = $result['AccessToken'];
    $_SESSION['auth_status'] = 'success';
    $_SESSION['auth_message'] = 'Successfully authenticated';
    
} catch (Exception $e) {
    $_SESSION['auth_status'] = 'error';
    $_SESSION['auth_message'] = 'Authentication failed: ' . $e->getMessage();
}

header('Location: /');
exit;
?> 