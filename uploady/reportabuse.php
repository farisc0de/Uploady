<?php include_once 'session.php'; ?>

<?php include_once "logic/reportAbuseLogic.php"; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <form method="post" action="">
                <div class="card">
                    <div class="card-header">
                        <b><?= $lang['report_abuse_title'] ?></b>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <div class="mb-3">
                                <input type="email" class="form-control" name="emailaddress" placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="fileurl" placeholder="<?= $lang['file_url_placeholder'] ?>" value="<?= $_GET['file_id'] ?? null ?>">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="fileabusenote" cols="30" rows="10" placeholder="<?= $lang['file_abuse_notes_placeholder'] ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer mb-0">
                        <button class="btn btn-primary" type="submit">
                            <?= $lang['report_abuse_button'] ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>