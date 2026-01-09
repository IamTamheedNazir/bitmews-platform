<?php
/**
 * BitMews Auto-Installer
 * Professional installation wizard for cPanel/VPS deployment
 * 
 * @package BitMews
 * @version 1.0.0
 */

session_start();

// Prevent access if already installed
if (file_exists('../.env') && !isset($_GET['force'])) {
    die('Application is already installed. Delete .env file to reinstall or add ?force=1 to URL.');
}

// Configuration
define('INSTALLER_VERSION', '1.0.0');
define('APP_NAME', 'BitMews');
define('MIN_PHP_VERSION', '8.2.0');
define('REQUIRED_EXTENSIONS', [
    'BCMath', 'Ctype', 'Fileinfo', 'JSON', 'Mbstring', 
    'OpenSSL', 'PDO', 'Tokenizer', 'XML', 'cURL', 'GD', 'Redis'
]);

// Get current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Installation Wizard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .installer-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .installer-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .installer-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .progress-bar {
            background: rgba(255,255,255,0.2);
            height: 4px;
            margin-top: 20px;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: white;
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .installer-body {
            padding: 40px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }
        
        .step.completed .step-number {
            background: #10b981;
            color: white;
        }
        
        .step-label {
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .step.active .step-label {
            color: #667eea;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group small {
            display: block;
            margin-top: 6px;
            color: #666;
            font-size: 12px;
        }
        
        .requirement-list {
            list-style: none;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #e0e0e0;
        }
        
        .requirement-item.success {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        
        .requirement-item.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        .requirement-item.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        
        .requirement-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .requirement-item.success .requirement-icon {
            color: #10b981;
        }
        
        .requirement-item.error .requirement-icon {
            color: #ef4444;
        }
        
        .requirement-item.warning .requirement-icon {
            color: #f59e0b;
        }
        
        .requirement-details {
            flex: 1;
        }
        
        .requirement-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }
        
        .requirement-status {
            font-size: 12px;
            color: #666;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
        }
        
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #10b981;
            color: #065f46;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }
        
        .alert-warning {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        
        .alert-info {
            background: #eff6ff;
            border: 1px solid #3b82f6;
            color: #1e40af;
        }
        
        .alert-icon {
            margin-right: 12px;
            font-size: 20px;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
            
            .installer-body {
                padding: 24px;
            }
            
            .step-label {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><?php echo APP_NAME; ?></h1>
            <p>Professional Installation Wizard v<?php echo INSTALLER_VERSION; ?></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo ($step / 6) * 100; ?>%"></div>
            </div>
        </div>
        
        <div class="installer-body">
            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">Requirements</div>
                </div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">Permissions</div>
                </div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">Database</div>
                </div>
                <div class="step <?php echo $step >= 4 ? 'active' : ''; ?> <?php echo $step > 4 ? 'completed' : ''; ?>">
                    <div class="step-number">4</div>
                    <div class="step-label">Admin</div>
                </div>
                <div class="step <?php echo $step >= 5 ? 'active' : ''; ?> <?php echo $step > 5 ? 'completed' : ''; ?>">
                    <div class="step-number">5</div>
                    <div class="step-label">Settings</div>
                </div>
                <div class="step <?php echo $step >= 6 ? 'active' : ''; ?>">
                    <div class="step-number">6</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>
            
            <?php
            // Include step files
            switch ($step) {
                case 1:
                    include 'steps/requirements.php';
                    break;
                case 2:
                    include 'steps/permissions.php';
                    break;
                case 3:
                    include 'steps/database.php';
                    break;
                case 4:
                    include 'steps/admin.php';
                    break;
                case 5:
                    include 'steps/settings.php';
                    break;
                case 6:
                    include 'steps/complete.php';
                    break;
                default:
                    include 'steps/requirements.php';
            }
            ?>
        </div>
    </div>
    
    <script>
        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading"></span> Processing...';
                }
            });
        });
        
        // Test database connection
        function testDatabaseConnection() {
            const form = document.getElementById('database-form');
            const formData = new FormData(form);
            const testBtn = document.getElementById('test-connection');
            const resultDiv = document.getElementById('test-result');
            
            testBtn.disabled = true;
            testBtn.innerHTML = '<span class="loading"></span> Testing...';
            resultDiv.innerHTML = '';
            
            fetch('ajax/test-database.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><span class="alert-icon">✓</span><div>Database connection successful!</div></div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-error"><span class="alert-icon">✗</span><div>' + data.message + '</div></div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-error"><span class="alert-icon">✗</span><div>Connection failed: ' + error.message + '</div></div>';
            })
            .finally(() => {
                testBtn.disabled = false;
                testBtn.innerHTML = 'Test Connection';
            });
        }
    </script>
</body>
</html>
