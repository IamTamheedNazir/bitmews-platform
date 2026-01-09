<?php
/**
 * AJAX: Run Database Seeders
 */

header('Content-Type: application/json');

try {
    // Change to Laravel root directory
    chdir('../../');
    
    // Run seeders
    $output = [];
    $returnCode = 0;
    
    exec('php artisan db:seed --force 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception('Seeding failed: ' . implode("\n", $output));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database seeded successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
