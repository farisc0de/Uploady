<?php
include_once 'session.php';
include_once APP_PATH . 'logic/signupLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <div class="text-center mb-4">
                <h1 class="h3 mb-3 fw-normal"><?= $lang["general"]['create_account'] ?? 'Create Account'; ?></h1>
                <p class="text-muted"><?= $lang["general"]['signup_subtitle'] ?? 'Join Uploady to start sharing files'; ?></p>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        <?= $lang["general"]['signup_title']; ?>
                    </h5>
                </div>

                <div class="card-body p-4">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    <?php if (isset($msg)) : ?>
                        <?= $utils->alert($msg, 'success', 'check-circle'); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label for="username" class="form-label"><?= $lang["general"]['username'] ?? 'Username'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="<?= $lang["general"]['enter_username']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label"><?= $lang["general"]['email'] ?? 'Email'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="<?= $lang["general"]['enter_your_email']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label"><?= $lang["general"]['password'] ?? 'Password'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="<?= $lang["general"]['enter_password']; ?>" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggle-password" data-bs-toggle="tooltip" title="<?= $lang["general"]['show_password'] ?? 'Show password'; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="password-strength-meter">
                                <div class="progress" style="height: 5px;">
                                    <progress id="password-progress" class="w-100" value="0" max="100" style="height: 5px;"></progress>
                                </div>
                                <small class="form-text text-muted" id="password-strength-text"><?= $lang["general"]['password_strength'] ?? 'Password strength'; ?></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="tos" required>
                                <label class="form-check-label" for="tos">
                                    <?= $lang["general"]['i_agree'] ?> <a href="page.php?s=terms" class="text-decoration-none"><?= $lang["general"]['tos_short'] ?></a>
                                </label>
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
                                <i class="fas fa-user-plus me-2"></i><?= $lang["general"]['signup_button'] ?>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer py-3 text-center">
                    <p class="mb-0">
                        <a href="<?= $utils->siteUrl('/login.php'); ?>" class="fw-bold text-decoration-none">
                            <?= $lang["general"]['login_cta_msg']; ?>
                        </a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="fas fa-shield-alt me-1"></i>
                    <?= $lang["general"]['secure_signup'] ?? 'Your information is secure with Uploady'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Password Toggle and Strength Script -->
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

        // Password strength meter
        const strengthMeter = document.getElementById('password-progress');
        const strengthText = document.getElementById('password-strength-text');

        if (password && strengthMeter && strengthText) {
            password.addEventListener('input', function() {
                const val = password.value;
                let strength = 0;
                let status = '';

                if (val.length >= 8) strength += 25;
                if (val.match(/[a-z]+/)) strength += 25;
                if (val.match(/[A-Z]+/)) strength += 25;
                if (val.match(/[0-9]+/) || val.match(/[^a-zA-Z0-9]+/)) strength += 25;

                // Update the strength meter
                strengthMeter.value = strength;

                // Update color based on strength
                if (strength < 25) {
                    strengthMeter.className = 'w-100 bg-danger';
                    status = '<?= $lang["general"]["password_weak"] ?? "Weak"; ?>';
                } else if (strength < 50) {
                    strengthMeter.className = 'w-100 bg-warning';
                    status = '<?= $lang["general"]["password_fair"] ?? "Fair"; ?>';
                } else if (strength < 75) {
                    strengthMeter.className = 'w-100 bg-info';
                    status = '<?= $lang["general"]["password_good"] ?? "Good"; ?>';
                } else {
                    strengthMeter.className = 'w-100 bg-success';
                    status = '<?= $lang["general"]["password_strong"] ?? "Strong"; ?>';
                }

                strengthText.textContent = '<?= $lang["general"]["password_strength"] ?? "Password strength"; ?>: ' + status;
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