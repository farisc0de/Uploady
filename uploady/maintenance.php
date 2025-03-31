<?php include_once 'session.php'; ?>

<?php $title = $lang['general']['maintenance_mode_title'] ?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-tools fa-4x text-warning"></i>
                    </div>
                    <h2 class="mb-3 fw-bold"><?= $lang['general']['maintenance_mode_title'] ?></h2>
                    <p class="lead mb-4"><?= $lang['general']['maintenance_mode_body'] ?></p>
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span><?= $lang['general']['please_wait'] ?? 'Please check back soon' ?></span>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-center py-3">
                    <a href="<?= $utils->siteUrl(); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt me-2"></i> <?= $lang['general']['refresh_page'] ?? 'Refresh Page' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>