<?php
include_once 'session.php';
include_once APP_PATH . 'logic/resetPasswordLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        <?= $lang["general"]['reset_password_title']; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($msg)) : ?>
                        <?php echo $utils->alert($msg, "primary", "info-circle"); ?>
                    <?php endif; ?>

                    <?php if (isset($err)) : ?>
                        <?php echo $utils->alert($err, "danger", "times-circle"); ?>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 border rounded-circle mb-3">
                            <i class="fas fa-lock-open fa-2x"></i>
                        </div>
                        <h4><?= $lang["general"]['reset_password_header']; ?></h4>
                        <p class="text-muted"><?= $lang["general"]['reset_password_msg']; ?></p>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="password" class="form-label"><?= $lang["general"]['new_password'] ?></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" 
                                    pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" 
                                    title="<?= $lang["general"]['password_pattern_msg'] ?>" 
                                    placeholder="<?= $lang["general"]['enter_new_password'] ?? 'Enter new password' ?>" 
                                    required="required">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <small><i class="fas fa-info-circle me-1"></i><?= $lang["general"]['password_requirements'] ?? 'Must be at least 8 characters with uppercase, lowercase, and number or symbol' ?></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label"><?= $lang["general"]['confirm_password']; ?></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="confirmPassword" id="confirmPassword" 
                                    pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" 
                                    title="<?= $lang["general"]['password_pattern_msg'] ?>" 
                                    class="form-control" 
                                    placeholder="<?= $lang["general"]['confirm_new_password'] ?? 'Confirm new password' ?>" 
                                    required="required">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-check-circle me-2"></i><?= $lang["general"]['reset_password_button']; ?>
                            </button>
                            <a href="login.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i><?= $lang["general"]['back_to_login'] ?? 'Back to Login'; ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        
        if (toggleConfirmPassword && confirmPassword) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                
                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Password match validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('<?= $lang["general"]['passwords_dont_match'] ?? "Passwords don't match!" ?>');
                }
            });
        }
    });
</script>

<?php include_once 'components/footer.php'; ?>