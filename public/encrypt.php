<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Crittora\CrittoraSDK;

session_start();

if (!isset($_SESSION['auth_token'])) {
    $_SESSION['auth_status'] = 'error';
    $_SESSION['auth_message'] = 'Please authenticate first';
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data'])) {
    try {
        $sdk = new CrittoraSDK();
        $encryptedData = $sdk->encrypt(
            $_SESSION['auth_token'],
            $_POST['data']
        );
        $_SESSION['encrypted_data'] = $encryptedData;
    } catch (Exception $e) {
        $_SESSION['encrypt_error'] = $e->getMessage();
    }
}

header('Location: /');
exit; 