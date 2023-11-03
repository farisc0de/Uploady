<?php
include_once 'session.php';
include_once  APP_PATH . 'logic/authLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <b><?= $lang["general"]['two_factor_title']; ?></b>
                </div>
                <div class="card-body container text-left">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    <form method="POST" id="login_form">
                        <div class="mb-3">
                            <input type="text" maxlength="6" max="6" class="form-control" name="otp_code" placeholder="<?= $lang["general"]['please_enter_your_code']; ?>">
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remberme" name="remberme">
                            <label class="custom-control-label" for="remberme"><?= $lang["general"]['trust_device']; ?></label>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <?= $lang["general"]['login_button']; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>