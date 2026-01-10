<?php
/**
 * AJAX: Create Environment File
 */

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $dbConfig = $data['db_config'];
    $appConfig = $data['app_config'];
    
    // Determine the correct path to .env.example
    $possiblePaths = [
        __DIR__ . '/../../.env.example',  // From install/ajax/
        dirname(dirname(__DIR__)) . '/.env.example',  // Absolute from install/ajax/
        $_SERVER['DOCUMENT_ROOT'] . '/.env.example',  // From document root
    ];
    
    $envExample = false;
    $envExamplePath = null;
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $envExample = file_get_contents($path);
            $envExamplePath = $path;
            break;
        }
    }
    
    if (!$envExample) {
        // If .env.example not found, create a basic one
        $envExample = "APP_NAME=BitMews
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC
APP_CURRENCY=USD

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitmews
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\"\${APP_NAME}\"
VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"
VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"
VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"
VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";
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
    
    // Determine .env file path
    $envPaths = [
        __DIR__ . '/../../.env',
        dirname(dirname(__DIR__)) . '/.env',
        $_SERVER['DOCUMENT_ROOT'] . '/.env',
    ];
    
    $envPath = null;
    foreach ($envPaths as $path) {
        $dir = dirname($path);
        if (is_writable($dir)) {
            $envPath = $path;
            break;
        }
    }
    
    if (!$envPath) {
        throw new Exception('Cannot find writable directory for .env file');
    }
    
    // Write .env file
    $result = file_put_contents($envPath, $env);
    
    if ($result === false) {
        throw new Exception('Failed to create .env file. Check permissions on: ' . dirname($envPath));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Environment file created successfully',
        'path' => $envPath,
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
