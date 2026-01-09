<?php
/**
 * AJAX: Run Database Migrations
 */

header('Content-Type: application/json');

try {
    // Change to Laravel root directory
    chdir('../../');
    
    // Run migrations
    $output = [];
    $returnCode = 0;
    
    exec('php artisan migrate --force 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception('Migration failed: ' . implode("\n", $output));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database migrations completed successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
