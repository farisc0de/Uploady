<?php
include_once '../session.php';
include_once 'logic/authLogic.php';
?>

<?php include_once '../components/header.php'; ?>
<title>
    <?= $st['website_name'] ?> - <?= $lang["enable_two_factor_title"]; ?>
</title>
<?php include_once '../components/css.php'; ?>
</head>

<body>

    <?php include_once '../components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <b><?= $lang['manage_two_factor_title']; ?></b>
                            </div>
                            <form method="POST" action="actions/auth.php">
                                <div class="card-body">
                                    <?= $utils->input('id', $data->id); ?>
                                    <?= $utils->input('csrf', $_SESSION['csrf']); ?>
                                    <?php if (isset($_GET['msg'])) : ?>
                                        <?php if ($_GET['msg'] == "yes") : ?>
                                            <?php echo $utils->alert(
                                                $lang["enable_two_factor_success"],
                                                "success",
                                                "check-circle"
                                            ); ?>
                                        <?php elseif ($_GET['msg'] == "csrf") : ?>

                                            <?php echo $utils->alert(
                                                $lang["csrf_error"],
                                                "danger",
                                                "times-circle"
                                            ); ?>
                                        <?php elseif ($_GET['msg'] == "error") : ?>
                                            <?php echo $utils->alert(
                                                $lang['unexpected_error'],
                                                "danger",
                                                "times-circle"
                                            ); ?>
                                        <?php elseif ($_GET['msg'] == "attack") : ?>
                                            <?php echo $utils->alert(
                                                $lang['attack_error'],
                                                "danger",
                                                "times-circle"
                                            ); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!$user->isTwoFAEnabled($username)) : ?>
                                        <p>
                                            2FA is an enhanced level of security for your account. Each time you login, an extra step where you will need to enter a unique code will be required to gain access to your account. To enable 2FA please click the button below and download the <b>Google Authenticator</b> app from Apple Store or Google Play Store

                                        <h4>Important</h4>

                                        You need to scan the code below with the app. You need to backup the QR code below by saving it and save the key somewhere safe in case you lose your phone. You will not be able to login if you can't provide the code. if you disable 2FA and re-enable it, you will need to scan a new code.
                                        </p>

                                        <img src="<?php echo $auth->getQRCodeImageAsDataUri("Uploady", $secret); ?>" class="rounded" />
                                        <div class="mb-3 mt-2">
                                            <input type="text" class="form-control" name="otp_secret" readonly value="<?= $secret ?>">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="otp_code" placeholder="<?= $lang['two_factor_code']; ?>">
                                            <small for="otp_code" class="small">
                                                <?= $lang['get_code']; ?>
                                            </small>
                                        </div>
                                        <button name="enable" class="btn btn-primary">
                                            <?= $lang['enable_two_factor_btn'] ?>
                                        </button>
                                    <?php else : ?>
                                        <button name="disable" class="btn btn-danger">
                                            <?= $lang['disable_two_factor_btn'] ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../components/footer.php'; ?>

    <?php include_once '../components/js.php'; ?>
</body>

</html>