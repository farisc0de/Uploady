<?php include_once 'session.php'; ?>

<?php include_once "logic/reportAbuseLogic.php"; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <form method="post" action="actions/reportabuse.php">
                <div class="card">
                    <div class="card-header">
                        <b><?= $lang["general"]['report_abuse_title'] ?></b>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['msg'])) : ?>
                            <?php if ($_GET['msg'] == 'report_sent') : ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle"></i> <?= $lang["general"]['report_abuse_success']; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($_GET['msg'] == 'file_not_found') : ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-times-circle"></i> <?= $lang["general"]['report_file_not_found'] ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="container">
                            <div class="mb-3">
                                <input type="email" class="form-control" name="emailaddress" placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="fileid" placeholder="<?= $lang["general"]['file_url_placeholder'] ?>" value="<?= $fileID ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="fileabusenote" cols="30" rows="10" placeholder="<?= $lang["general"]['file_abuse_notes_placeholder'] ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer mb-0">
                        <button class="btn btn-primary" type="submit">
                            <?= $lang["general"]['report_abuse_button'] ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>