<?php
/**
 * AJAX: Create Storage Link
 */

header('Content-Type: application/json');

try {
    // Change to Laravel root directory
    chdir('../../');
    
    // Create storage link
    $output = [];
    $returnCode = 0;
    
    exec('php artisan storage:link 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception('Failed to create storage link: ' . implode("\n", $output));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Storage link created successfully',
        'output' => $output,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
