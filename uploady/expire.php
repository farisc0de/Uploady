<?php include_once 'session.php'; ?>

<?php $title = $lang["general"]['token_expired_title']; ?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $lang["general"]['token_expired_title']; ?>
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <div class="d-inline-block p-3 rounded-circle mb-3" style="background-color: rgba(var(--bs-danger-rgb), 0.1);">
                            <i class="fas fa-clock fa-3x text-danger"></i>
                        </div>
                        <h3 class="mb-3"><?= $lang["general"]['token_expired_head'] ?></h3>
                        <p class="lead mb-4">
                            <?= $lang["general"]['token_expired_msg']; ?>
                        </p>
                    </div>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="alert-heading"><?= $lang["general"]['what_to_do'] ?? 'What to do next?'; ?></h5>
                                <p class="mb-0"><?= $lang["general"]['token_expired_help'] ?? 'You can request a new password reset link by clicking the button below.'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-3">
                    <div class="d-grid gap-2 mb-3">
                        <a class="btn btn-primary" href="forgot-password.php">
                            <i class="fas fa-key me-2"></i><?= $lang["general"]['request_new_link'] ?? 'Request New Reset Link'; ?>
                        </a>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i><?= $lang["general"]['login_button'] ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>