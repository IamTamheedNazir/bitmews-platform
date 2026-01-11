<?php
/**
 * AJAX: Run Database Seeders
 */

header('Content-Type: application/json');

try {
    // Find Laravel root directory
    $possibleRoots = [
        __DIR__ . '/../..',
        dirname(dirname(__DIR__)),
        $_SERVER['DOCUMENT_ROOT'],
    ];
    
    $laravelRoot = null;
    foreach ($possibleRoots as $root) {
        if (file_exists($root . '/artisan')) {
            $laravelRoot = $root;
            break;
        }
    }
    
    if (!$laravelRoot) {
        throw new Exception('Cannot find Laravel root directory');
    }
    
    // Change to Laravel root directory
    chdir($laravelRoot);
    
    // Check if .env exists
    if (!file_exists($laravelRoot . '/.env')) {
        throw new Exception('.env file not found');
    }
    
    // Try different PHP executables
    $phpExecutables = [
        'php',
        '/usr/bin/php',
        '/usr/local/bin/php',
        'php82', 'php81', 'php80',
        'php8.2', 'php8.1', 'php8.0',
        '/opt/cpanel/ea-php82/root/usr/bin/php',
        '/opt/cpanel/ea-php81/root/usr/bin/php',
        '/opt/cpanel/ea-php80/root/usr/bin/php',
    ];
    
    $seederSuccess = false;
    $lastError = '';
    
    foreach ($phpExecutables as $php) {
        $checkCmd = "which $php 2>/dev/null || command -v $php 2>/dev/null";
        $phpPath = trim(shell_exec($checkCmd));
        
        if (empty($phpPath)) {
            continue;
        }
        
        $output = [];
        $command = "$php artisan db:seed --force 2>&1";
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $seederSuccess = true;
            break;
        }
        
        $lastError = implode("\n", $output);
        
        if (strpos($lastError, 'command not found') === false && 
            strpos($lastError, 'not found') === false) {
            break;
        }
    }
    
    if (!$seederSuccess) {
        throw new Exception('Seeding failed: ' . $lastError);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database seeded successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
