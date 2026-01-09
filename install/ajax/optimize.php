<?php
/**
 * AJAX: Optimize Application
 */

header('Content-Type: application/json');

try {
    // Change to Laravel root directory
    chdir('../../');
    
    $commands = [
        'php artisan config:cache',
        'php artisan route:cache',
        'php artisan view:cache',
    ];
    
    $allOutput = [];
    
    foreach ($commands as $command) {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception('Optimization failed: ' . implode("\n", $output));
        }
        
        $allOutput = array_merge($allOutput, $output);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Application optimized successfully',
        'output' => $allOutput,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
