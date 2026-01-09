<?php
/**
 * Step 2: File Permissions Check
 */

// Directories that need to be writable
$directories = [
    '../storage' => 'Storage Directory',
    '../storage/app' => 'Storage App',
    '../storage/framework' => 'Storage Framework',
    '../storage/framework/cache' => 'Cache Directory',
    '../storage/framework/sessions' => 'Sessions Directory',
    '../storage/framework/views' => 'Views Directory',
    '../storage/logs' => 'Logs Directory',
    '../bootstrap/cache' => 'Bootstrap Cache',
    '../public/uploads' => 'Uploads Directory',
];

// Check permissions
$permissions = [];
$allPermissionsOk = true;

foreach ($directories as $dir => $name) {
    $exists = file_exists($dir);
    $writable = $exists && is_writable($dir);
    
    $permissions[] = [
        'name' => $name,
        'path' => $dir,
        'exists' => $exists,
        'writable' => $writable,
        'status' => $exists && $writable
    ];
    
    if (!$exists || !$writable) {
        $allPermissionsOk = false;
    }
}

// Check .env file
$envExists = file_exists('../.env');
$envWritable = !$envExists || is_writable('../.env');
$envStatus = $envWritable;

if (!$envWritable) {
    $allPermissionsOk = false;
}

?>

<h2 style="margin-bottom: 24px; color: #333;">File Permissions Check</h2>

<?php if ($allPermissionsOk): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <div>
            <strong>Perfect!</strong> All directories have the correct permissions.
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <span class="alert-icon">⚠</span>
        <div>
            <strong>Permission Issues Detected!</strong> Please fix the permissions below.
            <br><br>
            <strong>How to fix:</strong>
            <br>Run this command via SSH: <code>chmod -R 775 storage bootstrap/cache public/uploads</code>
            <br>Or use your hosting control panel's file manager to set permissions to 775.
        </div>
    </div>
<?php endif; ?>

<h3 style="margin: 32px 0 16px; color: #333; font-size: 18px;">Directory Permissions</h3>
<ul class="requirement-list">
    <?php foreach ($permissions as $perm): ?>
        <li class="requirement-item <?php echo $perm['status'] ? 'success' : ($perm['exists'] ? 'warning' : 'error'); ?>">
            <div class="requirement-icon">
                <?php echo $perm['status'] ? '✓' : ($perm['exists'] ? '⚠' : '✗'); ?>
            </div>
            <div class="requirement-details">
                <div class="requirement-name"><?php echo $perm['name']; ?></div>
                <div class="requirement-status">
                    <?php echo $perm['path']; ?> - 
                    <?php 
                    if (!$perm['exists']) {
                        echo 'Directory does not exist';
                    } elseif (!$perm['writable']) {
                        echo 'Not writable (chmod 775 required)';
                    } else {
                        echo 'Writable ✓';
                    }
                    ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    
    <li class="requirement-item <?php echo $envStatus ? 'success' : 'warning'; ?>">
        <div class="requirement-icon"><?php echo $envStatus ? '✓' : '⚠'; ?></div>
        <div class="requirement-details">
            <div class="requirement-name">.env Configuration File</div>
            <div class="requirement-status">
                <?php echo $envExists ? 'Exists' : 'Will be created'; ?> - 
                <?php echo $envWritable ? 'Writable ✓' : 'Not writable (chmod 664 required)'; ?>
            </div>
        </div>
    </li>
</ul>

<div class="alert alert-info" style="margin-top: 24px;">
    <span class="alert-icon">ℹ</span>
    <div>
        <strong>Note:</strong> If you're using cPanel, you can set permissions using the File Manager:
        <ol style="margin-top: 8px; margin-left: 20px;">
            <li>Right-click on the directory</li>
            <li>Select "Change Permissions"</li>
            <li>Set to 775 (rwxrwxr-x)</li>
            <li>Check "Recurse into subdirectories"</li>
        </ol>
    </div>
</div>

<div class="button-group">
    <a href="index.php?step=1" class="btn btn-secondary">← Previous Step</a>
    <a href="index.php?step=3" class="btn btn-primary <?php echo !$allPermissionsOk ? 'disabled' : ''; ?>"
       <?php echo !$allPermissionsOk ? 'onclick="return false;"' : ''; ?>>
        Continue to Database Setup →
    </a>
</div>

<style>
    code {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
    
    ol {
        font-size: 14px;
        line-height: 1.6;
    }
</style>
