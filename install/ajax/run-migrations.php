<?php
/**
 * AJAX: Run Database Migrations
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
        if (file_exists($root . '/artisan') && file_exists($root . '/vendor/autoload.php')) {
            $laravelRoot = $root;
            break;
        }
    }
    
    if (!$laravelRoot) {
        throw new Exception('Cannot find Laravel root directory. Make sure vendor folder exists (run: composer install)');
    }
    
    // Change to Laravel root directory
    chdir($laravelRoot);
    
    // Check if .env exists
    if (!file_exists($laravelRoot . '/.env')) {
        throw new Exception('.env file not found. Please complete previous steps first.');
    }
    
    // Check if vendor folder exists
    if (!file_exists($laravelRoot . '/vendor/autoload.php')) {
        throw new Exception('Composer dependencies not installed. Please run: composer install');
    }
    
    // Check if migrations folder exists
    if (!file_exists($laravelRoot . '/database/migrations')) {
        throw new Exception('Migrations folder not found at: ' . $laravelRoot . '/database/migrations');
    }
    
    // Run migrations
    $output = [];
    $returnCode = 0;
    
    // Try different PHP executables
    $phpExecutables = ['php', '/usr/bin/php', '/usr/local/bin/php', 'php8.1', 'php8.0', 'php8.2'];
    $migrationSuccess = false;
    $lastError = '';
    
    foreach ($phpExecutables as $php) {
        $output = [];
        exec("$php artisan migrate --force 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            $migrationSuccess = true;
            break;
        }
        
        $lastError = implode("\n", $output);
    }
    
    if (!$migrationSuccess) {
        // Try to get more detailed error
        $detailedError = $lastError;
        
        // Check if it's a database connection issue
        if (strpos($detailedError, 'Access denied') !== false) {
            throw new Exception('Database connection failed. Please check your database credentials in previous step.');
        }
        
        if (strpos($detailedError, 'Unknown database') !== false) {
            throw new Exception('Database does not exist. Please create the database first in cPanel.');
        }
        
        if (strpos($detailedError, 'SQLSTATE') !== false) {
            throw new Exception('Database error: ' . $detailedError);
        }
        
        throw new Exception('Migration failed: ' . $detailedError);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database migrations completed successfully',
        'output' => $output,
        'tables_created' => '40+ tables created',
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'help' => 'Check: 1) Database exists, 2) Credentials correct, 3) vendor folder exists (composer install)',
    ]);
}
