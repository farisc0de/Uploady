<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body"><?= $count_files; ?> Total Files</div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= $utils->siteUrl('/admin/files/view.php'); ?>">
                    View Details
                </a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body"><?= $count_user; ?> Total Users</div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= $utils->siteUrl('/admin/users/view.php'); ?>">
                    View Details
                </a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body"><?= $count_pages; ?> Total Pages</div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= $utils->siteUrl('/admin/pages/view.php'); ?>">
                    View Details
                </a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body"><?= $count_downloads ?> Total Downloads</div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= $utils->siteUrl('/admin/files/view.php'); ?>">
                    View Details
                </a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Uploads per Month (Year <?= date("Y"); ?>)
            </div>
            <div class="card-body">
                <canvas id="myBarChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Uploads per Country
            </div>
            <div class="card-body">
                <div id="world-map" style="width: 100%; height: 232px"></div>
            </div>
        </div>
    </div>
</div>