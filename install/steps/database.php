<?php
/**
 * Step 3: Database Configuration
 */

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['db_config'] = [
        'connection' => $_POST['db_connection'],
        'host' => $_POST['db_host'],
        'port' => $_POST['db_port'],
        'database' => $_POST['db_database'],
        'username' => $_POST['db_username'],
        'password' => $_POST['db_password'],
    ];
    
    // Redirect to next step
    header('Location: index.php?step=4');
    exit;
}

// Get saved values or defaults
$dbConfig = $_SESSION['db_config'] ?? [
    'connection' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'bitmews',
    'username' => 'root',
    'password' => '',
];

?>

<h2 style="margin-bottom: 24px; color: #333;">Database Configuration</h2>

<div class="alert alert-info">
    <span class="alert-icon">ℹ</span>
    <div>
        <strong>Database Setup:</strong> Enter your database connection details below.
        <br>Make sure the database exists or check "Create database if not exists" option.
        <br><br>
        <strong>cPanel Users:</strong> Create database via cPanel → MySQL Databases
    </div>
</div>

<form method="POST" id="database-form">
    <div class="form-group">
        <label for="db_connection">Database Type *</label>
        <select name="db_connection" id="db_connection" required onchange="updatePort(this.value)">
            <option value="mysql" <?php echo $dbConfig['connection'] === 'mysql' ? 'selected' : ''; ?>>MySQL</option>
            <option value="pgsql" <?php echo $dbConfig['connection'] === 'pgsql' ? 'selected' : ''; ?>>PostgreSQL</option>
        </select>
        <small>Select your database type. MySQL is recommended for most users.</small>
    </div>
    
    <div class="two-column">
        <div class="form-group">
            <label for="db_host">Database Host *</label>
            <input type="text" name="db_host" id="db_host" value="<?php echo htmlspecialchars($dbConfig['host']); ?>" required>
            <small>Usually "localhost" or "127.0.0.1"</small>
        </div>
        
        <div class="form-group">
            <label for="db_port">Database Port *</label>
            <input type="text" name="db_port" id="db_port" value="<?php echo htmlspecialchars($dbConfig['port']); ?>" required>
            <small>MySQL: 3306, PostgreSQL: 5432</small>
        </div>
    </div>
    
    <div class="form-group">
        <label for="db_database">Database Name *</label>
        <input type="text" name="db_database" id="db_database" value="<?php echo htmlspecialchars($dbConfig['database']); ?>" required>
        <small>The name of your database (e.g., bitmews_db)</small>
    </div>
    
    <div class="two-column">
        <div class="form-group">
            <label for="db_username">Database Username *</label>
            <input type="text" name="db_username" id="db_username" value="<?php echo htmlspecialchars($dbConfig['username']); ?>" required>
            <small>Your database username</small>
        </div>
        
        <div class="form-group">
            <label for="db_password">Database Password</label>
            <input type="password" name="db_password" id="db_password" value="<?php echo htmlspecialchars($dbConfig['password']); ?>">
            <small>Your database password (leave empty if none)</small>
        </div>
    </div>
    
    <div id="test-result"></div>
    
    <div style="margin: 24px 0;">
        <button type="button" class="btn btn-secondary" id="test-connection" onclick="testDatabaseConnection()">
            Test Connection
        </button>
    </div>
    
    <div class="alert alert-warning">
        <span class="alert-icon">⚠</span>
        <div>
            <strong>Important:</strong> Make sure to test the connection before proceeding.
            The installer will create all necessary tables automatically.
        </div>
    </div>
    
    <div class="button-group">
        <a href="index.php?step=2" class="btn btn-secondary">← Previous Step</a>
        <button type="submit" class="btn btn-primary">
            Continue to Admin Setup →
        </button>
    </div>
</form>

<script>
function updatePort(type) {
    const portInput = document.getElementById('db_port');
    if (type === 'mysql') {
        portInput.value = '3306';
    } else if (type === 'pgsql') {
        portInput.value = '5432';
    }
}
</script>
