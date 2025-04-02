<?php include_once 'session.php';
include_once APP_PATH . 'logic/activationLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        <?= $lang["general"]['activation_title'] ?>
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <div class="d-inline-block p-3 border rounded-circle mb-3">
                            <?php if (strpos($msg, 'success') !== false || strpos($msg, 'activated') !== false) : ?>
                                <i class="fas fa-check-circle fa-3x text-success"></i>
                            <?php else : ?>
                                <i class="fas fa-exclamation-circle fa-3x text-warning"></i>
                            <?php endif; ?>
                        </div>
                        
                        <h4 class="mb-3">
                            <?php if (strpos($msg, 'success') !== false || strpos($msg, 'activated') !== false) : ?>
                                <?= $lang["general"]['activation_success'] ?? 'Account Activated'; ?>
                            <?php else : ?>
                                <?= $lang["general"]['activation_failed'] ?? 'Activation Status'; ?>
                            <?php endif; ?>
                        </h4>
                        
                        <div class="alert <?php echo (strpos($msg, 'success') !== false || strpos($msg, 'activated') !== false) ? 'alert-success' : 'alert-warning'; ?> mb-4">
                            <?= $msg; ?>
                        </div>
                        
                        <p class="text-muted">
                            <?php if (strpos($msg, 'success') !== false || strpos($msg, 'activated') !== false) : ?>
                                <?= $lang["general"]['activation_success_message'] ?? 'Your account has been successfully activated. You can now log in and start using all features.'; ?>
                            <?php else : ?>
                                <?= $lang["general"]['activation_failed_message'] ?? 'If you continue to experience issues, please contact support.'; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="card-footer p-3 d-grid">
                    <a class="btn btn-primary" href="<?= $utils->siteUrl('/login.php'); ?>">
                        <i class="fas fa-sign-in-alt me-2"></i><?= $lang["general"]['go_to_login']; ?>
                    </a>
                </div>
            </div>
            
            <!-- Additional Options -->
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-home me-1"></i> <?= $lang["general"]['back_to_home'] ?? 'Back to Home'; ?>
                </a>
                <a href="signup.php" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="fas fa-user-plus me-1"></i> <?= $lang["general"]['create_account'] ?? 'Create Account'; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>