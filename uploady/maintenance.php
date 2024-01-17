<?php include_once 'session.php'; ?>

<?php $title = $lang['general']['maintenance_mode_title'] ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <b><?= $lang['general']['maintenance_mode_title'] ?></b>
                </div>
                <div class="card-body">
                    <p><?= $lang['general']['maintenance_mode_body'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>