<?php
/**
 * AJAX: Test Database Connection
 */

header('Content-Type: application/json');

$connection = $_POST['db_connection'] ?? 'mysql';
$host = $_POST['db_host'] ?? 'localhost';
$port = $_POST['db_port'] ?? '3306';
$database = $_POST['db_database'] ?? '';
$username = $_POST['db_username'] ?? '';
$password = $_POST['db_password'] ?? '';

try {
    if ($connection === 'mysql') {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$database}'");
        $dbExists = $stmt->rowCount() > 0;
        
        if (!$dbExists) {
            // Try to create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $message = "Database connection successful! Database '{$database}' was created.";
        } else {
            $message = "Database connection successful! Database '{$database}' exists.";
        }
        
    } elseif ($connection === 'pgsql') {
        $dsn = "pgsql:host={$host};port={$port}";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        // Check if database exists
        $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '{$database}'");
        $dbExists = $stmt->rowCount() > 0;
        
        if (!$dbExists) {
            // Try to create database
            $pdo->exec("CREATE DATABASE \"{$database}\" ENCODING 'UTF8'");
            $message = "Database connection successful! Database '{$database}' was created.";
        } else {
            $message = "Database connection successful! Database '{$database}' exists.";
        }
    } else {
        throw new Exception('Unsupported database type');
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'database_exists' => $dbExists ?? false,
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $e->getMessage(),
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
