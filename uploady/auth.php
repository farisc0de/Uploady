<?php
include_once 'session.php';
include_once  APP_PATH . 'logic/authLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        <?= $lang["general"]['two_factor_title']; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 border rounded-circle mb-3">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                        <h4><?= $lang["general"]['verification_code'] ?? 'Verification Code'; ?></h4>
                        <p class="text-muted"><?= $lang["general"]['enter_2fa_code_msg'] ?? 'Please enter the 6-digit code from your authenticator app'; ?></p>
                    </div>
                    
                    <form method="POST" id="login_form">
                        <div class="mb-4">
                            <label for="otp_code" class="form-label"><?= $lang["general"]['verification_code'] ?? 'Verification Code'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="form-control form-control-lg text-center" 
                                    id="otp_code" name="otp_code" placeholder="000000" required autofocus>
                            </div>
                            <div id="codeHelp" class="form-text">
                                <small><i class="fas fa-info-circle me-1"></i><?= $lang["general"]['code_expires'] ?? 'This code expires after 30 seconds'; ?></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remberme" name="remberme">
                                <label class="form-check-label" for="remberme"><?= $lang["general"]['trust_device']; ?></label>
                            </div>
                            <div class="form-text">
                                <small><?= $lang["general"]['trust_device_info'] ?? 'You won\'t be asked for the code on this device for 30 days'; ?></small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i><?= $lang["general"]['login_button']; ?>
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
        // Auto-focus the OTP input field
        document.getElementById('otp_code').focus();
        
        // Auto-submit when 6 digits are entered
        document.getElementById('otp_code').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Optional: auto-submit the form when 6 digits are entered
                // document.getElementById('login_form').submit();
            }
        });
    });
</script>

<?php include_once 'components/footer.php'; ?>