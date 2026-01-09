<?php
/**
 * Step 4: Admin Account Setup
 */

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['admin_config'] = [
        'name' => $_POST['admin_name'],
        'email' => $_POST['admin_email'],
        'password' => $_POST['admin_password'],
    ];
    
    // Redirect to next step
    header('Location: index.php?step=5');
    exit;
}

// Get saved values or defaults
$adminConfig = $_SESSION['admin_config'] ?? [
    'name' => 'Admin',
    'email' => 'admin@bitmews.com',
    'password' => '',
];

?>

<h2 style="margin-bottom: 24px; color: #333;">Create Admin Account</h2>

<div class="alert alert-info">
    <span class="alert-icon">ℹ</span>
    <div>
        <strong>Super Admin Account:</strong> This will be your main administrator account.
        <br>You can create additional admin accounts later from the admin panel.
        <br><br>
        <strong>⚠️ Important:</strong> Please use a strong password and remember these credentials!
    </div>
</div>

<form method="POST" id="admin-form" onsubmit="return validateForm()">
    <div class="form-group">
        <label for="admin_name">Full Name *</label>
        <input type="text" name="admin_name" id="admin_name" 
               value="<?php echo htmlspecialchars($adminConfig['name']); ?>" 
               required minlength="3">
        <small>Your full name (e.g., John Doe)</small>
    </div>
    
    <div class="form-group">
        <label for="admin_email">Email Address *</label>
        <input type="email" name="admin_email" id="admin_email" 
               value="<?php echo htmlspecialchars($adminConfig['email']); ?>" 
               required>
        <small>This will be your login email. Make sure it's valid!</small>
    </div>
    
    <div class="form-group">
        <label for="admin_password">Password *</label>
        <input type="password" name="admin_password" id="admin_password" 
               required minlength="8">
        <small>Minimum 8 characters, include uppercase, lowercase, numbers, and special characters</small>
        <div id="password-strength" style="margin-top: 8px;"></div>
    </div>
    
    <div class="form-group">
        <label for="admin_password_confirmation">Confirm Password *</label>
        <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" 
               required minlength="8">
        <small>Re-enter your password to confirm</small>
    </div>
    
    <div class="alert alert-warning">
        <span class="alert-icon">⚠</span>
        <div>
            <strong>Security Tip:</strong> Use a password manager to generate and store a strong password.
            <br>Never share your admin credentials with anyone!
        </div>
    </div>
    
    <div class="button-group">
        <a href="index.php?step=3" class="btn btn-secondary">← Previous Step</a>
        <button type="submit" class="btn btn-primary">
            Continue to Settings →
        </button>
    </div>
</form>

<script>
function validateForm() {
    const password = document.getElementById('admin_password').value;
    const confirmation = document.getElementById('admin_password_confirmation').value;
    
    if (password !== confirmation) {
        alert('Passwords do not match!');
        return false;
    }
    
    // Check password strength
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    if (!hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar) {
        alert('Password must contain uppercase, lowercase, numbers, and special characters!');
        return false;
    }
    
    return true;
}

// Password strength indicator
document.getElementById('admin_password').addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('password-strength');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    
    const colors = ['#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981', '#10b981'];
    const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    
    if (password.length > 0) {
        strengthDiv.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="flex: 1; height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden;">
                    <div style="width: ${(strength / 6) * 100}%; height: 100%; background: ${colors[strength - 1]}; transition: all 0.3s;"></div>
                </div>
                <span style="font-size: 12px; color: ${colors[strength - 1]}; font-weight: 600;">${labels[strength - 1]}</span>
            </div>
        `;
    } else {
        strengthDiv.innerHTML = '';
    }
});
</script>
