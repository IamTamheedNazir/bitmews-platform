<?php
/**
 * Auto-Install Composer Dependencies
 * 
 * This script automatically installs composer dependencies
 * Run this BEFORE the installer if vendor folder is missing
 */

set_time_limit(600); // 10 minutes
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Install Dependencies - BitMews</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .status { 
            padding: 15px; 
            border-radius: 8px; 
            margin: 15px 0;
            font-size: 14px;
            line-height: 1.6;
        }
        .status.info { background: #e3f2fd; color: #1976d2; border-left: 4px solid #1976d2; }
        .status.success { background: #e8f5e9; color: #388e3c; border-left: 4px solid #388e3c; }
        .status.error { background: #ffebee; color: #c62828; border-left: 4px solid #c62828; }
        .status.warning { background: #fff3e0; color: #f57c00; border-left: 4px solid #f57c00; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover { background: #5568d3; }
        .btn.success { background: #388e3c; }
        .btn.success:hover { background: #2e7d32; }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 12px;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .step { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 6px; }
        .step-title { font-weight: 600; color: #333; margin-bottom: 10px; }
        .step-content { color: #666; font-size: 14px; }
        code { 
            background: #f5f5f5; 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Install Dependencies</h1>
        <div class="subtitle">BitMews Platform - Composer Dependency Installer</div>

        <?php
        $rootDir = __DIR__;
        $vendorExists = file_exists($rootDir . '/vendor/autoload.php');
        $composerJsonExists = file_exists($rootDir . '/composer.json');
        
        // Check if install action is requested
        $action = $_GET['action'] ?? '';
        
        if ($action === 'install' && !$vendorExists) {
            echo '<div class="status info"><div class="loading"></div> Installing composer dependencies... This may take 3-5 minutes. Please wait...</div>';
            echo '<pre>';
            
            // Try to install
            $composerCommands = [
                'composer install --no-dev --optimize-autoloader 2>&1',
                '/usr/local/bin/composer install --no-dev --optimize-autoloader 2>&1',
                'php /usr/local/bin/composer install --no-dev --optimize-autoloader 2>&1',
                'php composer.phar install --no-dev --optimize-autoloader 2>&1',
            ];
            
            $installed = false;
            foreach ($composerCommands as $cmd) {
                echo "Trying: $cmd\n";
                $output = [];
                $returnCode = 0;
                exec("cd $rootDir && $cmd", $output, $returnCode);
                
                echo implode("\n", $output) . "\n\n";
                
                if ($returnCode === 0 && file_exists($rootDir . '/vendor/autoload.php')) {
                    $installed = true;
                    break;
                }
            }
            
            echo '</pre>';
            
            if ($installed) {
                echo '<div class="status success">✅ <strong>Success!</strong> Composer dependencies installed successfully!</div>';
                echo '<a href="../install" class="btn success">Continue to Installer →</a>';
            } else {
                echo '<div class="status error">❌ <strong>Automatic installation failed.</strong> Please install manually.</div>';
                echo '<div class="step">';
                echo '<div class="step-title">Manual Installation Steps:</div>';
                echo '<div class="step-content">';
                echo '<strong>Option 1: Via SSH/Terminal</strong><br>';
                echo '<code>cd ' . $rootDir . '</code><br>';
                echo '<code>composer install --no-dev --optimize-autoloader</code><br><br>';
                echo '<strong>Option 2: Upload vendor folder</strong><br>';
                echo '1. On your computer: <code>composer install</code><br>';
                echo '2. Upload the generated <code>vendor</code> folder via FTP<br>';
                echo '3. Place in: <code>' . $rootDir . '/vendor</code>';
                echo '</div>';
                echo '</div>';
            }
            
        } elseif ($vendorExists) {
            // Vendor already exists
            echo '<div class="status success">✅ <strong>Dependencies already installed!</strong></div>';
            echo '<div class="status info">';
            echo '<strong>Vendor folder found:</strong> ' . $rootDir . '/vendor<br>';
            echo '<strong>Size:</strong> ' . formatBytes(dirSize($rootDir . '/vendor')) . '<br>';
            echo '<strong>Files:</strong> ' . countFiles($rootDir . '/vendor') . '+';
            echo '</div>';
            echo '<a href="../install" class="btn success">Continue to Installer →</a>';
            
        } else {
            // Show install options
            if (!$composerJsonExists) {
                echo '<div class="status error">❌ <strong>Error:</strong> composer.json not found!</div>';
                echo '<div class="status warning">Make sure all project files are uploaded correctly.</div>';
            } else {
                echo '<div class="status warning">⚠️ <strong>Vendor folder is missing!</strong></div>';
                echo '<div class="status info">';
                echo 'The <code>vendor</code> folder contains all Laravel dependencies (50-100MB).<br>';
                echo 'It must be installed before running the installer.';
                echo '</div>';
                
                echo '<div class="step">';
                echo '<div class="step-title">📦 What is the vendor folder?</div>';
                echo '<div class="step-content">';
                echo 'The vendor folder contains:<br>';
                echo '• Laravel framework<br>';
                echo '• All PHP packages and dependencies<br>';
                echo '• Required for the application to run<br>';
                echo '• Generated from composer.json';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="step">';
                echo '<div class="step-title">🚀 Installation Options:</div>';
                echo '<div class="step-content">';
                
                echo '<strong>Option 1: Automatic (Try this first)</strong><br>';
                echo '<a href="?action=install" class="btn">Install Dependencies Automatically</a><br><br>';
                
                echo '<strong>Option 2: Via Terminal/SSH</strong><br>';
                echo '<code>cd ' . $rootDir . '</code><br>';
                echo '<code>composer install --no-dev --optimize-autoloader</code><br><br>';
                
                echo '<strong>Option 3: Via cPanel Terminal</strong><br>';
                echo '1. Open cPanel → Terminal<br>';
                echo '2. Run: <code>cd public_html</code><br>';
                echo '3. Run: <code>composer install --no-dev</code><br><br>';
                
                echo '<strong>Option 4: Upload vendor folder</strong><br>';
                echo '1. On your computer: <code>composer install</code><br>';
                echo '2. Upload <code>vendor</code> folder via FTP<br>';
                echo '3. Place in: <code>' . $rootDir . '/vendor</code>';
                
                echo '</div>';
                echo '</div>';
            }
        }
        
        // Helper functions
        function formatBytes($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, $precision) . ' ' . $units[$pow];
        }
        
        function dirSize($dir) {
            $size = 0;
            if (!is_dir($dir)) return 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
                $size += $file->getSize();
            }
            return $size;
        }
        
        function countFiles($dir) {
            if (!is_dir($dir)) return 0;
            $count = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
                $count++;
            }
            return $count;
        }
        ?>
    </div>
</body>
</html>
