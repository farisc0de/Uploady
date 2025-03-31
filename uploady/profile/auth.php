<?php
include_once '../session.php';
include_once 'logic/authLogic.php';
?>

<?php include_once '../components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><?= $lang["general"]['manage_two_factor_title']; ?></h2>
                <a href="account.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= $lang["general"]['back_to_account'] ?? 'Back to Account'; ?>
                </a>
            </div>

            <?php if (isset($_GET['msg'])) : ?>
                <div class="mb-4">
                    <?php if ($_GET['msg'] == "two_factor_enabled") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]["enable_two_factor_success"],
                            "success",
                            "check-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "two_factor_disabled") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]["disable_two_factor_success"],
                            "success",
                            "check-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "csrf") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]["csrf_error"],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "error") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['unexpected_error'],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "attack") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['attack_error'],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        <?= $lang["general"]['two_factor_authentication'] ?? 'Two-Factor Authentication'; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="actions/auth.php">
                        <?= $utils->input('id', $data->id); ?>
                        <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                        <?php if (!$user->isTwoFAEnabled($username)) : ?>
                            <div class="row">
                                <div class="col-lg-7 mb-4 mb-lg-0">
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2"><?= $lang["general"]['what_is_2fa'] ?? 'What is Two-Factor Authentication?'; ?></h5>
                                        <p class="text-muted">
                                            2FA is an enhanced level of security for your account. Each time you login, an extra step where you will need to enter a unique code will be required to gain access to your account.
                                        </p>
                                        <p class="text-muted">
                                            To enable 2FA, download the <b>Google Authenticator</b> app from Apple Store or Google Play Store and scan the QR code.
                                        </p>
                                    </div>

                                    <div class="alert alert-warning">
                                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i><?= $lang["general"]['important'] ?? 'Important'; ?></h5>
                                        <p class="mb-0">
                                            You need to scan the code with the app. Please backup the QR code by saving it and keep the key somewhere safe in case you lose your phone. 
                                            <strong>You will not be able to login if you can't provide the code.</strong> If you disable 2FA and re-enable it, you will need to scan a new code.
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="otp_secret" class="form-label"><?= $lang["general"]['backup_key'] ?? 'Backup Key'; ?></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="otp_secret" name="otp_secret" readonly value="<?= $secret ?>">
                                            <button class="btn btn-outline-secondary" type="button" id="copy-secret" data-bs-toggle="tooltip" title="<?= $lang["general"]['copy_to_clipboard'] ?? 'Copy to clipboard'; ?>">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="form-text"><?= $lang["general"]['save_this_key'] ?? 'Save this key in a secure location'; ?></div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="otp_code" class="form-label"><?= $lang["general"]['verification_code'] ?? 'Verification Code'; ?></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            <input type="text" class="form-control" id="otp_code" name="otp_code" placeholder="<?= $lang["general"]['two_factor_code']; ?>">
                                        </div>
                                        <div class="form-text"><?= $lang["general"]['get_code']; ?></div>
                                    </div>

                                    <div class="d-grid">
                                        <button name="enable" class="btn btn-primary">
                                            <i class="fas fa-lock me-2"></i><?= $lang["general"]['enable_two_factor_btn'] ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center">
                                    <div class="card border p-3 mb-3">
                                        <div class="qr-code-container">
                                            <img src="<?php echo $auth->getQRCodeImageAsDataUri("Uploady: " . $_SESSION['username'], $secret); ?>" class="img-fluid rounded" alt="QR Code" />
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            <i class="fas fa-mobile-alt me-1"></i> <?= $lang["general"]['scan_with_app'] ?? 'Scan with Google Authenticator'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="text-center py-4">
                                <div class="mb-4">
                                    <div class="d-inline-block p-3 border rounded-circle mb-3">
                                        <i class="fas fa-lock text-success fa-3x"></i>
                                    </div>
                                    <h4><?= $lang["general"]['2fa_enabled'] ?? 'Two-Factor Authentication is Enabled'; ?></h4>
                                    <p class="text-muted">
                                        <?= $lang["general"]['2fa_enabled_desc'] ?? 'Your account is currently protected with two-factor authentication.'; ?>
                                    </p>
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= $lang["general"]['disable_warning'] ?? 'Disabling two-factor authentication will make your account less secure.'; ?>
                                </div>
                                <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                                    <button name="disable" class="btn btn-danger">
                                        <i class="fas fa-unlock me-2"></i><?= $lang["general"]['disable_two_factor_btn'] ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Secret Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Copy secret key to clipboard
        var copyButton = document.getElementById('copy-secret');
        if (copyButton) {
            copyButton.addEventListener('click', function() {
                var secretInput = document.getElementById('otp_secret');
                secretInput.select();
                document.execCommand('copy');
                
                // Update tooltip
                var tooltip = bootstrap.Tooltip.getInstance(copyButton);
                copyButton.setAttribute('data-bs-original-title', '<?= $lang["general"]['copied'] ?? 'Copied!'; ?>');
                if (tooltip) tooltip.update();
                
                // Reset tooltip after 2 seconds
                setTimeout(function() {
                    copyButton.setAttribute('data-bs-original-title', '<?= $lang["general"]['copy_to_clipboard'] ?? 'Copy to clipboard'; ?>');
                    if (tooltip) tooltip.update();
                }, 2000);
            });
        }
    });
</script>

<?php include_once '../components/footer.php'; ?>