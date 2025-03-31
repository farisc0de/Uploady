<?php
include_once 'session.php';
include_once  APP_PATH . 'logic/loginLogic.php';
?>

<?php include_once APP_PATH . 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <div class="text-center mb-4">
                <h1 class="h3 mb-3 fw-normal"><?= $lang["general"]['welcome_back'] ?? 'Welcome Back'; ?></h1>
                <p class="text-muted"><?= $lang["general"]['login_subtitle'] ?? 'Sign in to access your account'; ?></p>
            </div>
            
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <?= $lang["general"]['login_title'] ?>
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    
                    <form method="POST" id="login_form">
                        <div class="mb-4">
                            <label for="username" class="form-label"><?= $lang["general"]['username'] ?? 'Username'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="<?= $lang["general"]['enter_username']; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label"><?= $lang["general"]['password'] ?? 'Password'; ?></label>
                                <a class="small text-decoration-none" href="forgot-password.php"><?= $lang["general"]['forget_password']; ?></a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="<?= $lang["general"]['enter_password']; ?>">
                                <button class="btn btn-outline-secondary" type="button" id="toggle-password" data-bs-toggle="tooltip" title="<?= $lang["general"]['show_password'] ?? 'Show password'; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if ($settings->getSettingValue('recaptcha_status') == true) : ?>
                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                            <div class="mb-4">
                                <div class="recaptcha-info small p-2 rounded border">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <?= $lang["general"]['protected_by_recaptcha'] ?? 'This site is protected by reCAPTCHA'; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-sign-in-alt me-2"></i><?= $lang["general"]['login_button']; ?>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer py-3 text-center">
                    <p class="mb-0">
                        <?= $lang["general"]['no_account'] ?? 'Don\'t have an account?'; ?> 
                        <a href="<?= $utils->siteUrl('/signup.php'); ?>" class="fw-bold text-decoration-none">
                            <?= $lang["general"]['signup_cta_msg']; ?>
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="fas fa-shield-alt me-1"></i> 
                    <?= $lang["general"]['secure_login'] ?? 'Secure login with Uploady'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const password = document.getElementById('password');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
                
                // Update tooltip
                const tooltip = bootstrap.Tooltip.getInstance(this);
                const newTitle = type === 'password' ? 
                    '<?= $lang["general"]['show_password'] ?? 'Show password'; ?>' : 
                    '<?= $lang["general"]['hide_password'] ?? 'Hide password'; ?>';
                this.setAttribute('data-bs-original-title', newTitle);
                if (tooltip) tooltip.update();
            });
        }
    });
</script>

<?php if ($settings->getSettingValue('recaptcha_status') == true) : ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $settings->getSettingValue('recaptcha_site_key'); ?>"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo $settings->getSettingValue('recaptcha_site_key'); ?>', {
                action: 'login_form'
            }).then(function(token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
<?php endif; ?>

<?php include_once 'components/footer.php'; ?>