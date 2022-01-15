<?php
include_once 'session.php';
include_once APP_PATH . 'logic/resetPasswordLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8" />
    <?php include_once 'components/header.php'; ?>
    <title>
        <?= $st['website_name'] ?> - Reset Password
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
                                <b>Reset Password</b>
                            </div>
                            <div class="card-body">
                                <?php if (isset($msg)) : ?>

                                    <?php echo $utils->alert($msg, "primary", "info-circle"); ?>

                                <?php endif; ?>

                                <?php if (isset($err)) : ?>

                                    <?php echo $utils->alert($err, "danger", "times-circle"); ?>

                                <?php endif; ?>
                                <h4>Reset Password</h4>
                                <p>Please enter a strong password that contains 8 characters and at least one special character</p>
                                <form method="POST" action="">

                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <input type="password" name="password" id="password" class="form-control" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Must contain at least one number, one uppercase letter, lowercase letter, one character, and at least 8 or more characters" placeholder="New Password" required="required" autofocus="autofocus">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <input type="password" name="confirmPassword" id="confirmPassword" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Must contain at least one number, one uppercase letter, lowercase letter, one special character, and at least 8 or more characters" class="form-control" placeholder="Confirm Password" required="required" autofocus="autofocus">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block" type="submit">Reset Password</button>
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