<?php
include_once 'session.php';
include_once APP_PATH . 'logic/signupLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <b><?= $lang["general"]['signup_title']; ?></b>
                </div>
                <div class="card-body">
                    <?php if (isset($error)) : ?>
                        <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                    <?php endif; ?>
                    <?php if (isset($msg)) : ?>
                        <?= $utils->alert($msg, 'success', 'check-circle'); ?>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" placeholder="<?= $lang["general"]['enter_username']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="<?= $lang["general"]['enter_your_email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="<?= $lang["general"]['enter_password']; ?>" required>
                        </div>
                        <div class="form-check m-3 text-start">
                            <input type="checkbox" class="form-check-input" id="tos" required>
                            <label class="form-check-label" for="tos">
                                <?= $lang["general"]['i_agree'] ?> <a href="page.php?s=terms"><?= $lang["general"]['tos_short'] ?></a>
                            </label>
                        </div>
                        <?php if ($settings->getSettingValue('recaptcha_status') == true) : ?>
                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        <?php endif; ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <?= $lang["general"]['signup_button'] ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer mb-0">
                    <a href="<?= $utils->siteUrl('/login.php'); ?>">
                        <?= $lang["general"]['login_cta_msg']; ?>
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