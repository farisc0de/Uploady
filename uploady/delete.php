<?php include_once 'session.php'; ?>

<?php include_once 'logic/deleteLogic.php'; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <?= $lang["general"]['delete_file_title'] ?>
                </div>
                <div class="card-body">
                    <div class="border border-primary bg-primary rounded">
                        <p class="pt-3 text-light"><?= $msg; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>