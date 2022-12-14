<?php
include_once 'session.php';
include_once APP_PATH . 'logic/resetPasswordLogic.php';
?>

<?php include_once 'components/header.php'; ?>
<title>
    <?= $st['website_name'] ?> - <?= $lang['reset_password_title']; ?>
</title>
<?php include_once 'components/css.php'; ?>
</head>

<body>

    <?php include_once 'components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <b><?= $lang['reset_password_title']; ?></b>
                            </div>
                            <div class="card-body">
                                <?php if (isset($msg)) : ?>

                                    <?php echo $utils->alert($msg, "primary", "info-circle"); ?>

                                <?php endif; ?>

                                <?php if (isset($err)) : ?>

                                    <?php echo $utils->alert($err, "danger", "times-circle"); ?>

                                <?php endif; ?>
                                <h4><?= $lang['reset_password_header']; ?></h4>
                                <p><?= $lang['reset_password_msg']; ?></p>
                                <form method="POST" action="">

                                    <div class="mb-3">
                                        <div class="form-label-group">
                                            <input type="password" name="password" id="password" class="form-control" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="<?= $lang['password_pattern_msg'] ?>" placeholder="<?= $lang['new_password'] ?>" required="required" autofocus="autofocus">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-label-group">
                                            <input type="password" name="confirmPassword" id="confirmPassword" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="<?= $lang['password_pattern_msg'] ?>" class="form-control" placeholder="<?= $lang['confirm_password']; ?>" required="required" autofocus="autofocus">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block" type="submit">
                                        <?= $lang['reset_password_button']; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'components/footer.php'; ?>

    <?php include_once 'components/js.php'; ?>
</body>

</html>