<?php
/**
 * AJAX: Generate Application Key
 */

header('Content-Type: application/json');

try {
    // Change to Laravel root directory
    chdir('../../');
    
    // Generate application key
    $output = [];
    $returnCode = 0;
    
    exec('php artisan key:generate --force 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception('Failed to generate application key: ' . implode("\n", $output));
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
