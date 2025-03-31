<div class="mt-5 pt-4"></div>

<footer class="footer bg-body-tertiary py-4 border-top">
    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Logo and About Section -->
            <div class="col-md-4 mb-3 mb-md-0 text-center text-md-start">
                <div class="mb-3">
                    <a href="<?= $utils->siteUrl(); ?>" class="text-decoration-none">
                        <?php if ($st['website_logo'] != null) : ?>
                            <img src="<?= $st['website_logo'] ?>" alt="<?= $st['website_name'] ?>" width="30" height="30" class="d-inline-block align-top me-2">
                        <?php else : ?>
                            <i class="fas fa-cloud-upload-alt me-2 text-primary"></i>
                        <?php endif; ?>
                        <span class="fw-bold"><?= $st['website_name'] ?></span>
                    </a>
                </div>
                <p class="text-muted small mb-0">
                    <?= $lang["general"]['footer_about'] ?? 'A modern file sharing platform for all your needs.' ?>
                </p>
            </div>
            
            <!-- Quick Links -->
            <div class="col-md-4 mb-3 mb-md-0 text-center">
                <h6 class="fw-bold mb-3"><?= $lang["general"]['quick_links'] ?? 'Quick Links' ?></h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="<?= $utils->siteUrl('/index.php') ?>" class="text-decoration-none"><?= $lang['navbar']['home'] ?></a></li>
                    <li class="mb-2"><a href="<?= $utils->siteUrl('/page.php?s=terms') ?>" class="text-decoration-none"><?= $lang['navbar']['tos'] ?></a></li>
                    <li class="mb-2"><a href="<?= $utils->siteUrl('/page.php?s=about') ?>" class="text-decoration-none"><?= $lang['navbar']['about'] ?></a></li>
                    <li><a href="<?= $utils->siteUrl('/supported.php') ?>" class="text-decoration-none"><?= $lang["general"]['supported_formats'] ?? 'Supported Formats' ?></a></li>
                </ul>
            </div>
            
            <!-- Social Media -->
            <div class="col-md-4 text-center text-md-end">
                <h6 class="fw-bold mb-3"><?= $lang["general"]['connect_with_us'] ?? 'Connect With Us' ?></h6>
                <div class="d-flex justify-content-center justify-content-md-end">
                    <?php if (!empty($st['twitter_link'])) : ?>
                        <a href="<?= $st['twitter_link'] ?>" class="btn btn-sm btn-outline-primary rounded-circle me-2" target="_blank" rel="noopener" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($st['instagram_link'])) : ?>
                        <a href="<?= $st['instagram_link'] ?>" class="btn btn-sm btn-outline-danger rounded-circle me-2" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($st['linkedin_link'])) : ?>
                        <a href="<?= $st['linkedin_link'] ?>" class="btn btn-sm btn-outline-primary rounded-circle me-2" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($st['github_link'])) : ?>
                        <a href="<?= $st['github_link'] ?>" class="btn btn-sm btn-outline-dark rounded-circle" target="_blank" rel="noopener" aria-label="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted small mb-0">
                    <?= $lang["general"]['copyright_text'] ?> &copy; <?= $st['website_name'] . " - " . date('Y') ?>
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-muted small mb-0">
                    <?= $lang["general"]['powered_by'] ?? 'Powered by' ?> <a href="https://github.com/farisc0de/Uploady" class="text-decoration-none" target="_blank">Uploady</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php include_once APP_PATH . 'components/js.php'; ?>

</body>

</html>