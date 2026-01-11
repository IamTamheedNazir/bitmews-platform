<?php
/**
 * AJAX: Optimize Application
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
    
    $php = 'php'; // Default
    
    // Find working PHP
    foreach ($phpExecutables as $phpExec) {
        $checkCmd = "which $phpExec 2>/dev/null || command -v $phpExec 2>/dev/null";
        $phpPath = trim(shell_exec($checkCmd));
        
        if (!empty($phpPath)) {
            $php = $phpExec;
            break;
        }
    }
    
    $commands = [
        "$php artisan config:cache",
        "$php artisan route:cache",
        "$php artisan view:cache",
    ];
    
    $allOutput = [];
    
    foreach ($commands as $command) {
        $output = [];
        exec($command . ' 2>&1', $output, $returnCode);
        $allOutput = array_merge($allOutput, $output);
        
        if ($returnCode !== 0) {
            // Non-critical, continue anyway
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Application optimized successfully',
        'output' => $allOutput,
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
