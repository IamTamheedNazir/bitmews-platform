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
        $errorMsg = "CRITICAL ERROR: vendor folder is missing!\n\n";
        $errorMsg .= "The vendor folder contains all Laravel dependencies and is REQUIRED.\n\n";
        $errorMsg .= "HOW TO FIX:\n\n";
        $errorMsg .= "Option 1 - Via Terminal/SSH:\n";
        $errorMsg .= "  cd " . $laravelRoot . "\n";
        $errorMsg .= "  composer install --no-dev --optimize-autoloader\n\n";
        $errorMsg .= "Option 2 - Via cPanel Terminal:\n";
        $errorMsg .= "  1. Open cPanel Terminal\n";
        $errorMsg .= "  2. Run: cd public_html\n";
        $errorMsg .= "  3. Run: composer install --no-dev\n\n";
        $errorMsg .= "Option 3 - Upload vendor folder:\n";
        $errorMsg .= "  1. On your computer: composer install\n";
        $errorMsg .= "  2. Upload vendor folder via FTP\n";
        $errorMsg .= "  3. Place in: " . $laravelRoot . "/vendor\n\n";
        $errorMsg .= "Option 4 - Use install script:\n";
        $errorMsg .= "  bash " . $laravelRoot . "/install-dependencies.sh\n\n";
        $errorMsg .= "After installing vendor folder, refresh this page to continue.";
        
        throw new Exception($errorMsg);
    }
    
    // Change to Laravel root directory
    chdir($laravelRoot);
    
    // Check if .env exists
    if (!file_exists($laravelRoot . '/.env')) {
        throw new Exception('.env file not found. Please complete previous steps first.');
    }
    
    // Check if migrations folder exists
    if (!file_exists($laravelRoot . '/database/migrations')) {
        throw new Exception('Migrations folder not found at: ' . $laravelRoot . '/database/migrations');
    }
    
    // Try to run migrations
    $output = [];
    $returnCode = 0;
    
    // Try different PHP executables
    $phpExecutables = ['php', '/usr/bin/php', '/usr/local/bin/php', 'php8.2', 'php8.1', 'php8.0'];
    $migrationSuccess = false;
    $lastError = '';
    
    foreach ($phpExecutables as $php) {
        $output = [];
        $command = "$php artisan migrate --force 2>&1";
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $migrationSuccess = true;
            break;
        }
        
        $lastError = implode("\n", $output);
        
        // If error mentions vendor/autoload, break early
        if (strpos($lastError, 'vendor/autoload') !== false) {
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
            throw new Exception('Database connection failed. Check your database credentials.');
        }
        
        if (strpos($detailedError, 'Unknown database') !== false) {
            throw new Exception('Database does not exist. Create the database in cPanel first.');
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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'help' => 'Most common issue: vendor folder missing. Run: composer install --no-dev --optimize-autoloader',
    ]);
}
