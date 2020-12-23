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
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/files/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    Manage Files
                </a>
                <a class="nav-link" href="<?= $utils->siteUrl('/admin/settings/view.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    Edit Settings
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?= $data->username; ?>
        </div>
    </nav>
</div>