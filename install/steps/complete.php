<?php
/**
 * Step 6: Installation Process & Complete
 */

// Check if we have all required session data
if (!isset($_SESSION['db_config']) || !isset($_SESSION['admin_config']) || !isset($_SESSION['app_config'])) {
    header('Location: index.php?step=1');
    exit;
}

$installing = !isset($_GET['done']);

?>

<?php if ($installing): ?>
    <h2 style="margin-bottom: 24px; color: #333;">Installing BitMews...</h2>
    
    <div class="alert alert-info">
        <span class="alert-icon">⏳</span>
        <div>
            <strong>Please wait...</strong> The installation is in progress.
            <br>This may take a few minutes. Do not close this window!
        </div>
    </div>
    
    <div id="installation-progress">
        <ul class="requirement-list" id="progress-list">
            <li class="requirement-item" id="step-env">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Creating environment file...</div>
                    <div class="requirement-status">Generating .env configuration</div>
                </div>
            </li>
            <li class="requirement-item" id="step-key">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Generating application key...</div>
                    <div class="requirement-status">Securing your application</div>
                </div>
            </li>
            <li class="requirement-item" id="step-migrations">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Running database migrations...</div>
                    <div class="requirement-status">Creating database tables</div>
                </div>
            </li>
            <li class="requirement-item" id="step-seeders">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Seeding sample data...</div>
                    <div class="requirement-status">Adding currencies, gateways, and sample content</div>
                </div>
            </li>
            <li class="requirement-item" id="step-admin">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Creating admin account...</div>
                    <div class="requirement-status">Setting up your administrator</div>
                </div>
            </li>
            <li class="requirement-item" id="step-storage">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Creating storage links...</div>
                    <div class="requirement-status">Setting up file storage</div>
                </div>
            </li>
            <li class="requirement-item" id="step-cache">
                <div class="requirement-icon"><span class="loading"></span></div>
                <div class="requirement-details">
                    <div class="requirement-name">Optimizing application...</div>
                    <div class="requirement-status">Caching configuration and routes</div>
                </div>
            </li>
        </ul>
    </div>
    
    <div id="installation-result" style="display: none;"></div>
    
    <script>
        // Start installation automatically
        window.addEventListener('load', function() {
            startInstallation();
        });
        
        async function startInstallation() {
            const steps = [
                { id: 'env', name: 'Creating environment file', endpoint: 'create-env.php' },
                { id: 'key', name: 'Generating application key', endpoint: 'generate-key.php' },
                { id: 'migrations', name: 'Running migrations', endpoint: 'run-migrations.php' },
                { id: 'seeders', name: 'Seeding data', endpoint: 'run-seeders.php' },
                { id: 'admin', name: 'Creating admin', endpoint: 'create-admin.php' },
                { id: 'storage', name: 'Creating storage links', endpoint: 'create-storage-link.php' },
                { id: 'cache', name: 'Optimizing', endpoint: 'optimize.php' },
            ];
            
            for (const step of steps) {
                await executeStep(step);
            }
            
            // Installation complete
            window.location.href = 'index.php?step=6&done=1';
        }
        
        async function executeStep(step) {
            const stepElement = document.getElementById('step-' + step.id);
            const icon = stepElement.querySelector('.requirement-icon');
            
            try {
                const response = await fetch('ajax/' + step.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        db_config: <?php echo json_encode($_SESSION['db_config']); ?>,
                        admin_config: <?php echo json_encode($_SESSION['admin_config']); ?>,
                        app_config: <?php echo json_encode($_SESSION['app_config']); ?>,
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    stepElement.classList.add('success');
                    icon.innerHTML = '✓';
                } else {
                    throw new Error(result.message || 'Unknown error');
                }
            } catch (error) {
                stepElement.classList.add('error');
                icon.innerHTML = '✗';
                
                document.getElementById('installation-result').innerHTML = `
                    <div class="alert alert-error">
                        <span class="alert-icon">✗</span>
                        <div>
                            <strong>Installation Failed!</strong><br>
                            Error in step: ${step.name}<br>
                            ${error.message}
                        </div>
                    </div>
                `;
                document.getElementById('installation-result').style.display = 'block';
                
                throw error;
            }
        }
    </script>

<?php else: ?>
    <!-- Installation Complete -->
    <h2 style="margin-bottom: 24px; color: #333;">🎉 Installation Complete!</h2>
    
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <div>
            <strong>Congratulations!</strong> BitMews has been successfully installed.
            <br>Your crypto intelligence platform is ready to use!
        </div>
    </div>
    
    <div style="background: #f8f9fa; padding: 24px; border-radius: 8px; margin: 24px 0;">
        <h3 style="margin-bottom: 16px; color: #333;">📝 Your Admin Credentials</h3>
        <div style="font-family: 'Courier New', monospace; background: white; padding: 16px; border-radius: 4px; border: 1px solid #e0e0e0;">
            <div style="margin-bottom: 8px;">
                <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['admin_config']['email']); ?>
            </div>
            <div>
                <strong>Password:</strong> (the password you set)
            </div>
        </div>
        <p style="margin-top: 12px; color: #666; font-size: 14px;">
            ⚠️ <strong>Important:</strong> Please save these credentials in a secure location!
        </p>
    </div>
    
    <div style="background: #fffbeb; padding: 24px; border-radius: 8px; margin: 24px 0; border: 1px solid #f59e0b;">
        <h3 style="margin-bottom: 16px; color: #92400e;">🔒 Security Recommendations</h3>
        <ol style="margin-left: 20px; color: #92400e; line-height: 1.8;">
            <li>Delete or rename the <code>/install</code> directory immediately</li>
            <li>Change your admin password after first login</li>
            <li>Set up SSL certificate (HTTPS) for your domain</li>
            <li>Configure email settings in admin panel</li>
            <li>Set up payment gateways and AI providers</li>
            <li>Review and update privacy policy and terms of service</li>
        </ol>
    </div>
    
    <div style="background: #eff6ff; padding: 24px; border-radius: 8px; margin: 24px 0; border: 1px solid #3b82f6;">
        <h3 style="margin-bottom: 16px; color: #1e40af;">🚀 Next Steps</h3>
        <ul style="margin-left: 20px; color: #1e40af; line-height: 1.8;">
            <li>Log in to admin panel and explore the dashboard</li>
            <li>Configure payment gateways (Stripe, Razorpay, Crypto, etc.)</li>
            <li>Add AI provider API keys (OpenAI, Gemini, Claude)</li>
            <li>Customize appearance (logo, colors, branding)</li>
            <li>Set up email templates and SMTP</li>
            <li>Add your first token listings</li>
            <li>Create sample news posts</li>
            <li>Test the platform thoroughly</li>
        </ul>
    </div>
    
    <div class="button-group" style="margin-top: 32px;">
        <a href="<?php echo $_SESSION['app_config']['app_url']; ?>" class="btn btn-secondary" target="_blank">
            View Website →
        </a>
        <a href="<?php echo $_SESSION['app_config']['app_url']; ?>/admin" class="btn btn-primary">
            Go to Admin Panel 🚀
        </a>
    </div>
    
    <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e0e0e0;">
        <p style="color: #666; font-size: 14px;">
            Need help? Check our <a href="https://docs.bitmews.com" target="_blank" style="color: #667eea;">documentation</a> 
            or contact <a href="mailto:support@bitmews.com" style="color: #667eea;">support</a>
        </p>
        <p style="color: #999; font-size: 12px; margin-top: 8px;">
            Made with ❤️ by BitMews Team
        </p>
    </div>
    
    <?php
    // Clear session data
    session_destroy();
    ?>
<?php endif; ?>

<style>
    code {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
</style>
