<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/index.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    Dashboard
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/users/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    Manage Users
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/roles/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    Manage Roles
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/files/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    Manage Files
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/pages/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    Manage Pages
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/languages/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    Manage Languages
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/settings/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    Edit Settings
                </a>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Pages
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                            Authentication
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                            Error
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?= $data->username; ?>
        </div>
    </nav>
</div>