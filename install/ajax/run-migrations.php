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
        if (file_exists($root . '/artisan')) {
            $laravelRoot = $root;
            break;
        }
    }
    
    if (!$laravelRoot) {
        throw new Exception('Cannot find Laravel root directory (artisan file not found)');
    }
    
    // Check if vendor folder exists
    if (!file_exists($laravelRoot . '/vendor/autoload.php')) {
        throw new Exception("vendor folder is missing! Run: composer install --no-dev --optimize-autoloader");
    }
    
    // Change to Laravel root directory
    chdir($laravelRoot);
    
    // Check if .env exists
    if (!file_exists($laravelRoot . '/.env')) {
        throw new Exception('.env file not found. Please complete previous steps first.');
    }
    
    // Try different PHP executables (most common first)
    $phpExecutables = [
        'php',           // Default
        '/usr/bin/php',  // Standard location
        '/usr/local/bin/php',
        'php82',         // PHP 8.2
        'php81',         // PHP 8.1
        'php80',         // PHP 8.0
        'php8.2',
        'php8.1',
        'php8.0',
        '/opt/cpanel/ea-php82/root/usr/bin/php',  // cPanel PHP 8.2
        '/opt/cpanel/ea-php81/root/usr/bin/php',  // cPanel PHP 8.1
        '/opt/cpanel/ea-php80/root/usr/bin/php',  // cPanel PHP 8.0
    ];
    
    $migrationSuccess = false;
    $lastError = '';
    $usedPhp = '';
    
    foreach ($phpExecutables as $php) {
        // Check if PHP executable exists
        $checkCmd = "which $php 2>/dev/null || command -v $php 2>/dev/null";
        $phpPath = trim(shell_exec($checkCmd));
        
        if (empty($phpPath)) {
            continue; // PHP executable not found, try next
        }
        
        $output = [];
        $command = "$php artisan migrate --force 2>&1";
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $migrationSuccess = true;
            $usedPhp = $php;
            break;
        }
        
        $lastError = implode("\n", $output);
        
        // If error is not about PHP command, break (it's a real error)
        if (strpos($lastError, 'command not found') === false && 
            strpos($lastError, 'not found') === false) {
            $usedPhp = $php;
            break;
        }
    }
    
    if (!$migrationSuccess) {
        // Parse error for better message
        $detailedError = $lastError;
        
        // Check for vendor/autoload error
        if (strpos($detailedError, 'vendor/autoload') !== false) {
            throw new Exception("vendor folder is missing or incomplete. Run: composer install --no-dev --optimize-autoloader");
        }
        
        // Check if it's a database connection issue
        if (strpos($detailedError, 'Access denied') !== false) {
            throw new Exception('Database connection failed. Check your database credentials in .env file.');
        }
        
        if (strpos($detailedError, 'Unknown database') !== false) {
            throw new Exception('Database does not exist. Create the database in cPanel first.');
        }
        
        if (strpos($detailedError, 'SQLSTATE') !== false) {
            throw new Exception('Database error: ' . $detailedError);
        }
        
        // Check if no PHP found at all
        if (strpos($detailedError, 'command not found') !== false || 
            strpos($detailedError, 'not found') !== false) {
            throw new Exception('PHP executable not found. Tried: ' . implode(', ', $phpExecutables) . '. Contact your hosting provider.');
        }
        
        throw new Exception('Migration failed: ' . $detailedError);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database migrations completed successfully',
        'output' => $output,
        'php_used' => $usedPhp,
        'tables_created' => '40+ tables created',
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'help' => 'Check database credentials and ensure PHP is available on your server.',
    ]);
}
