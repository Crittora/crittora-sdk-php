<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Crittora\CrittoraSDK;

session_start();

// Verify user authentication
if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_status'] = 'error';
    $_SESSION['auth_message'] = 'Please authenticate first.';
    header('Location: /');
    exit;
}

// Handle POST requests for data encryption
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data'])) {
    try {
        // Initialize the SDK
        $sdk = new CrittoraSDK();

        // Encrypt the provided data
        $encryptedData = $sdk->encrypt(
            $_SESSION['auth_token'],  // Pass the user's authentication token
            $_POST['data'],           // Data to encrypt
            ['read', 'write']         // Example permissions (optional)
        );

        // Store the encrypted result in the session
        $_SESSION['encrypted_data'] = $encryptedData;
        $_SESSION['encrypt_status'] = 'success';
    } catch (Exception $e) {
        // Handle encryption errors
        $_SESSION['encrypt_error'] = $e->getMessage();
        $_SESSION['encrypt_status'] = 'error';
    }
}

// Redirect to the home page
header('Location: /');
exit; 
