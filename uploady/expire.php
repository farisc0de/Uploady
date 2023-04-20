<?php include_once 'session.php'; ?>

<? $title = $lang['token_expired_title']; ?>

<?php include_once 'components/header.php'; ?>

<div id="wrapper">
    <div id="content-wrapper">
        <div class="container pb-5 pt-5">
            <div class="row justify-content-center text-center">
                <div class="col-sm-12 col-md-8 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <b><?= $lang['token_expired_title']; ?></b>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4><?= $lang['token_expired_head'] ?></h4>
                            </div>
                            <p class="lead text-center">
                                <?= $lang['token_expired_msg']; ?>
                            </p>
                        </div>
                        <div class="card-footer mb-0">
                            <div class="text-center">
                                <a class="d-block small mt-3" href="forgot-password.php">
                                    <?= $lang['forget_password_title'] ?>
                                </a>
                                <a class="d-block small" href="login.php">
                                    <?= $lang['login_button'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>