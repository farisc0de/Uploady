<?php include_once 'session.php';
include_once APP_PATH . 'logic/activationLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8" />
    <?php include_once 'components/header.php'; ?>
    <title>
        <?= $st['website_name'] ?> - <?= $lang['activation_title'] ?>
    </title>
    <?php include_once 'components/css.php'; ?>
</head>

<body>

    <?php include_once 'components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <b><?= $lang['activation_title'] ?></b>
                            </div>
                            <div class="card-body">
                                <div class="border border-primary bg-primary rounded">
                                    <p class="pt-3 text-light">
                                        <?= $msg; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="card-footer mb-0">
                                <a class="btn btn-primary" href="<?= $utils->siteUrl('/login.php'); ?>">
                                    <?= $lang['go_to_login']; ?>
                                </a>
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

</html>