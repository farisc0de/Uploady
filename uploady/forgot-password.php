<?php
include_once 'session.php';
include_once APP_PATH . 'logic/forgetPasswordLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <?php include_once 'components/header.php'; ?>
    <meta charset="UTF-8" />
    <title>
        <?= $st['website_name'] ?> - Forget Password
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
                                <b>Password Recovery</b>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)) : ?>
                                    <?= $utils->alert($error, 'danger', 'times-circle'); ?>
                                <?php endif; ?>
                                <?php if (isset($msg)) : ?>
                                    <?= $utils->alert($msg); ?>
                                <?php endif; ?>
                                <div class="text-center mb-4">
                                    <h4>Forgot your password?</h4>
                                    <p>Enter your email address and we will send you instructions on how to reset your password.</p>
                                </div>
                                <form method="POST" id="login_form">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="email" placeholder="Enter Email">
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            Reset Password
                                        </button>
                                    </div>
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

</html>