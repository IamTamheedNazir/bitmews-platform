<?php
/**
 * Step 5: Application Settings
 */

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['app_config'] = [
        'app_name' => $_POST['app_name'],
        'app_url' => $_POST['app_url'],
        'timezone' => $_POST['timezone'],
        'currency' => $_POST['currency'],
    ];
    
    // Redirect to installation process
    header('Location: index.php?step=6');
    exit;
}

// Auto-detect app URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$appUrl = $protocol . '://' . $host;

// Get saved values or defaults
$appConfig = $_SESSION['app_config'] ?? [
    'app_name' => 'BitMews',
    'app_url' => $appUrl,
    'timezone' => 'UTC',
    'currency' => 'USD',
];

// Timezone list (common ones)
$timezones = [
    'UTC' => 'UTC (Coordinated Universal Time)',
    'America/New_York' => 'America/New York (EST/EDT)',
    'America/Chicago' => 'America/Chicago (CST/CDT)',
    'America/Los_Angeles' => 'America/Los Angeles (PST/PDT)',
    'Europe/London' => 'Europe/London (GMT/BST)',
    'Europe/Paris' => 'Europe/Paris (CET/CEST)',
    'Asia/Dubai' => 'Asia/Dubai (GST)',
    'Asia/Kolkata' => 'Asia/Kolkata (IST)',
    'Asia/Singapore' => 'Asia/Singapore (SGT)',
    'Asia/Tokyo' => 'Asia/Tokyo (JST)',
    'Australia/Sydney' => 'Australia/Sydney (AEDT/AEST)',
];

// Currency list (common ones)
$currencies = [
    'USD' => 'USD - US Dollar',
    'EUR' => 'EUR - Euro',
    'GBP' => 'GBP - British Pound',
    'INR' => 'INR - Indian Rupee',
    'JPY' => 'JPY - Japanese Yen',
    'CNY' => 'CNY - Chinese Yuan',
    'AUD' => 'AUD - Australian Dollar',
    'CAD' => 'CAD - Canadian Dollar',
    'AED' => 'AED - UAE Dirham',
    'SGD' => 'SGD - Singapore Dollar',
];

?>

<h2 style="margin-bottom: 24px; color: #333;">Application Settings</h2>

<div class="alert alert-info">
    <span class="alert-icon">ℹ</span>
    <div>
        <strong>Basic Configuration:</strong> Set up your application's basic settings.
        <br>You can change these later from the admin panel.
    </div>
</div>

<form method="POST" id="settings-form">
    <div class="form-group">
        <label for="app_name">Application Name *</label>
        <input type="text" name="app_name" id="app_name" 
               value="<?php echo htmlspecialchars($appConfig['app_name']); ?>" 
               required>
        <small>The name of your application (e.g., BitMews, MyCryptoNews)</small>
    </div>
    
    <div class="form-group">
        <label for="app_url">Application URL *</label>
        <input type="url" name="app_url" id="app_url" 
               value="<?php echo htmlspecialchars($appConfig['app_url']); ?>" 
               required>
        <small>Your domain URL (e.g., https://yourdomain.com) - Auto-detected</small>
    </div>
    
    <div class="two-column">
        <div class="form-group">
            <label for="timezone">Default Timezone *</label>
            <select name="timezone" id="timezone" required>
                <?php foreach ($timezones as $value => $label): ?>
                    <option value="<?php echo $value; ?>" 
                            <?php echo $appConfig['timezone'] === $value ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Select your timezone</small>
        </div>
        
        <div class="form-group">
            <label for="currency">Default Currency *</label>
            <select name="currency" id="currency" required>
                <?php foreach ($currencies as $value => $label): ?>
                    <option value="<?php echo $value; ?>" 
                            <?php echo $appConfig['currency'] === $value ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Select your default currency</small>
        </div>
    </div>
    
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <div>
            <strong>Almost Done!</strong> Click "Install Now" to complete the installation.
            <br>This will create all database tables and set up your application.
        </div>
    </div>
    
    <div class="button-group">
        <a href="index.php?step=4" class="btn btn-secondary">← Previous Step</a>
        <button type="submit" class="btn btn-primary">
            Install Now 🚀
        </button>
    </div>
</form>
