<?php
/**
 * AJAX: Create Admin Account
 */

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $adminConfig = $data['admin_config'];
    
    // Change to Laravel root directory
    chdir('../../');
    
    // Create admin user using Artisan command
    $name = escapeshellarg($adminConfig['name']);
    $email = escapeshellarg($adminConfig['email']);
    $password = escapeshellarg($adminConfig['password']);
    
    $command = "php artisan bitmews:create-admin {$name} {$email} {$password} 2>&1";
    
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception('Failed to create admin account: ' . implode("\n", $output));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin account created successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
