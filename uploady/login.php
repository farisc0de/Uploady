<?php
include_once 'session.php';
include_once  APP_PATH . 'logic/loginLogic.php';
?>

<?php include_once APP_PATH . 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <b><?= $lang["general"]['login_title'] ?></b>
                </div>
                <div class="card-body container text-left">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    <form method="POST" id="login_form">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" placeholder="<?= $lang["general"]['enter_username']; ?>">
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="<?= $lang["general"]['enter_password']; ?>">
                        </div>
                        <?php if ($settings->getSettingValue('recaptcha_status') == true) : ?>
                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        <?php endif; ?>
                        <a class="d-block small m-3" href="forgot-password.php"><?= $lang["general"]['forget_password']; ?></a>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <?= $lang["general"]['login_button']; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer mb-0">
                    <a href="<?= $utils->siteUrl('/signup.php'); ?>">
                        <?= $lang["general"]['signup_cta_msg']; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($settings->getSettingValue('recaptcha_status') == true) : ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $settings->getSettingValue('recaptcha_site_key'); ?>"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo $settings->getSettingValue('recaptcha_site_key'); ?>', {
                action: 'login_form'
            }).then(function(token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
<?php endif; ?>

<?php include_once 'components/footer.php'; ?>