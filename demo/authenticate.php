<?php

require_once '../vendor/autoload.php'; // Adjust the path as necessary

use Crittora\CrittoraSDK;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

try {
    $sdk = new CrittoraSDK();
    $authResponse = $sdk->authenticate($username, $password);
    echo json_encode(['success' => true, 'IdToken' => $authResponse['IdToken']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 