<?php
/**
 * AJAX: Create Environment File
 */

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $dbConfig = $data['db_config'];
    $appConfig = $data['app_config'];
    
    // Read .env.example template
    $envExample = file_get_contents('../../.env.example');
    
    if (!$envExample) {
        throw new Exception('.env.example file not found');
    }
    
    // Replace placeholders
    $env = str_replace([
        'APP_NAME=BitMews',
        'APP_ENV=local',
        'APP_DEBUG=true',
        'APP_URL=http://localhost',
        
        'DB_CONNECTION=mysql',
        'DB_HOST=127.0.0.1',
        'DB_PORT=3306',
        'DB_DATABASE=bitmews',
        'DB_USERNAME=root',
        'DB_PASSWORD=',
        
        'APP_TIMEZONE=UTC',
        'APP_CURRENCY=USD',
    ], [
        'APP_NAME="' . $appConfig['app_name'] . '"',
        'APP_ENV=production',
        'APP_DEBUG=false',
        'APP_URL=' . $appConfig['app_url'],
        
        'DB_CONNECTION=' . $dbConfig['connection'],
        'DB_HOST=' . $dbConfig['host'],
        'DB_PORT=' . $dbConfig['port'],
        'DB_DATABASE=' . $dbConfig['database'],
        'DB_USERNAME=' . $dbConfig['username'],
        'DB_PASSWORD=' . $dbConfig['password'],
        
        'APP_TIMEZONE=' . $appConfig['timezone'],
        'APP_CURRENCY=' . $appConfig['currency'],
    ], $envExample);
    
    // Write .env file
    $result = file_put_contents('../../.env', $env);
    
    if ($result === false) {
        throw new Exception('Failed to create .env file. Check permissions.');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Environment file created successfully',
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
