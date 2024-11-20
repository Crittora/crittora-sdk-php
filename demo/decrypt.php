<?php

require_once '../vendor/autoload.php';

use Crittora\CrittoraSDK;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$idToken = $data['idToken'] ?? '';
$encryptedData = $data['encryptedData'] ?? '';
$permissions = $data['permissions'] ?? '';

try {
    $sdk = new CrittoraSDK();
    $decryptedData = $sdk->decrypt($idToken, $encryptedData, $permissions);
    echo json_encode(['success' => true, 'decryptedData' => $decryptedData]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 