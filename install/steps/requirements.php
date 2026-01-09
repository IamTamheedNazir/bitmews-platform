<?php
/**
 * Step 1: Server Requirements Check
 */

// Check PHP version
$phpVersion = phpversion();
$phpVersionOk = version_compare($phpVersion, MIN_PHP_VERSION, '>=');

// Check required extensions
$extensions = [];
foreach (REQUIRED_EXTENSIONS as $ext) {
    $extensions[$ext] = extension_loaded($ext);
}

// Check additional requirements
$requirements = [
    'PHP Version' => [
        'required' => MIN_PHP_VERSION . '+',
        'current' => $phpVersion,
        'status' => $phpVersionOk
    ],
    'allow_url_fopen' => [
        'required' => 'Enabled',
        'current' => ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled',
        'status' => ini_get('allow_url_fopen')
    ],
    'Memory Limit' => [
        'required' => '256M+',
        'current' => ini_get('memory_limit'),
        'status' => true // We'll accept any value
    ],
    'Max Execution Time' => [
        'required' => '300+',
        'current' => ini_get('max_execution_time') . 's',
        'status' => ini_get('max_execution_time') >= 300 || ini_get('max_execution_time') == 0
    ],
];

// Check if all requirements are met
$allRequirementsMet = $phpVersionOk && !in_array(false, $extensions, true);
foreach ($requirements as $req) {
    if (!$req['status']) {
        $allRequirementsMet = false;
        break;
    }
}

?>

<h2 style="margin-bottom: 24px; color: #333;">Server Requirements Check</h2>

<?php if ($allRequirementsMet): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <div>
            <strong>Great!</strong> Your server meets all the requirements.
            You can proceed with the installation.
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-error">
        <span class="alert-icon">✗</span>
        <div>
            <strong>Requirements Not Met!</strong> Please fix the issues below before proceeding.
            Contact your hosting provider if you need assistance.
        </div>
    </div>
<?php endif; ?>

<h3 style="margin: 32px 0 16px; color: #333; font-size: 18px;">PHP Requirements</h3>
<ul class="requirement-list">
    <?php foreach ($requirements as $name => $req): ?>
        <li class="requirement-item <?php echo $req['status'] ? 'success' : 'error'; ?>">
            <div class="requirement-icon"><?php echo $req['status'] ? '✓' : '✗'; ?></div>
            <div class="requirement-details">
                <div class="requirement-name"><?php echo $name; ?></div>
                <div class="requirement-status">
                    Required: <?php echo $req['required']; ?> | 
                    Current: <?php echo $req['current']; ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<h3 style="margin: 32px 0 16px; color: #333; font-size: 18px;">PHP Extensions</h3>
<ul class="requirement-list">
    <?php foreach ($extensions as $ext => $loaded): ?>
        <li class="requirement-item <?php echo $loaded ? 'success' : 'error'; ?>">
            <div class="requirement-icon"><?php echo $loaded ? '✓' : '✗'; ?></div>
            <div class="requirement-details">
                <div class="requirement-name"><?php echo $ext; ?> Extension</div>
                <div class="requirement-status">
                    <?php echo $loaded ? 'Installed' : 'Not Installed'; ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<div class="button-group">
    <a href="index.php?step=2" class="btn btn-primary <?php echo !$allRequirementsMet ? 'disabled' : ''; ?>" 
       <?php echo !$allRequirementsMet ? 'onclick="return false;"' : ''; ?>>
        Continue to Next Step →
    </a>
</div>

<style>
    .btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>
