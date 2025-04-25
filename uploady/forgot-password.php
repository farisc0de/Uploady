<?php
include_once 'session.php';
include_once APP_PATH . 'logic/forgetPasswordLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-unlock me-2"></i>
                        <?= $lang["general"]['forget_password_header']; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    <?php if (isset($msg)) : ?>
                        <?= $utils->alert($msg); ?>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 border rounded-circle mb-3">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h4><?= $lang["general"]['forget_password_h4'] ?></h4>
                        <p class="text-muted"><?= $lang["general"]['forget_password_msg']; ?></p>
                    </div>
                    
                    <form method="POST" id="login_form">
                        <div class="mb-4">
                            <label for="email" class="form-label"><?= $lang["general"]['email'] ?? 'Email'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                    placeholder="<?= $lang["general"]['enter_your_email']; ?>" required>
                            </div>
                            <div class="form-text">
                                <small><i class="fas fa-info-circle me-1"></i><?= $lang["general"]['password_reset_email_info'] ?? 'We will send a password reset link to this email address' ?></small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i><?= $lang["general"]['reset_password_button']; ?>
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

<?php include_once 'components/footer.php'; ?>