<?php
/**
 * AJAX: Generate Application Key
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
        throw new Exception('Cannot find Laravel root directory (artisan file not found)');
    }
    
    // Change to Laravel root directory
    chdir($laravelRoot);
    
    // Check if .env exists
    if (!file_exists($laravelRoot . '/.env')) {
        throw new Exception('.env file not found. Please create environment file first.');
    }
    
    // Generate application key
    $output = [];
    $returnCode = 0;
    
    // Try different PHP executables
    $phpExecutables = ['php', '/usr/bin/php', '/usr/local/bin/php', 'php8.1', 'php8.0'];
    $keyGenerated = false;
    
    foreach ($phpExecutables as $php) {
        exec("$php artisan key:generate --force 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            $keyGenerated = true;
            break;
        }
    }
    
    if (!$keyGenerated) {
        // Fallback: Generate key manually
        $key = 'base64:' . base64_encode(random_bytes(32));
        
        // Read .env
        $env = file_get_contents($laravelRoot . '/.env');
        
        // Replace APP_KEY
        $env = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $env);
        
        // Write back
        file_put_contents($laravelRoot . '/.env', $env);
        
        $output[] = 'Application key generated manually: ' . $key;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Application key generated successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
