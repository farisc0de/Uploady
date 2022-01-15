<?php include_once 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8" />
    <?php include_once 'components/header.php'; ?>
    <title>
        <?= $st['website_name'] ?> - Token Expired
    </title>
    <?php include_once 'components/css.php'; ?>
</head>

<body>

    <?php include_once 'components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <b>Website Template</b>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <h4>Key Expired</h4>
                                </div>
                                <p class="lead text-center">
                                    We are so sorry for the inconvenience but it appers your code does not exist or expired
                                    please reset your password again to generate a new token.
                                    </br></br>
                                    Thank You
                                </p>
                            </div>
                            <div class="card-footer mb-0">
                                <div class="text-center">
                                    <a class="d-block small mt-3" href="forgot-password.php">Forgot Password?</a>
                                    <a class="d-block small" href="login.php">Login Page</a>
                                </div>
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