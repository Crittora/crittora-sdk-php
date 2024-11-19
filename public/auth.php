<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Crittora\CrittoraSDK;

session_start();

try {
    $sdk = new CrittoraSDK();
    $result = $sdk->authenticate('demo_user', 'demo_password');
    
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