<?php
include_once 'config/config.php';
include_once APP_PATH . 'logic/installLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-language" content="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="application-name" content="Uploady" />
    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <meta name="language" content="EN" />
    <title>
        Uploady - Install Software
    </title>
    <?php
    $utils->style('https://bootswatch.com/5/litera/bootstrap.min.css');
    $utils->style('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css');
    $utils->style('css/custom.css');
    ?>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $utils->siteUrl(); ?>">
                Uploady
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <b>Software Installation</b>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if (isset($msg)) : ?>
                                        <?php echo $utils->alert("Script has been installed.", "success", "check-circle"); ?>
                                    <?php endif; ?>

                                    <?php if (isset($error)) : ?>
                                        <?php echo $utils->alert($error, "danger", "times-circle"); ?>
                                    <?php endif; ?>

                                    <?php if (!isset($_POST['install'])) : ?>

                                        <?php echo $php_alert; ?>

                                        <div class="text-center alert alert-primary border-primary">
                                            <b>
                                                <div>
                                                    PHP Version: <?php echo PHP_VERSION; ?>
                                                    <br />
                                                    <?php foreach ($is_installed as $library) : ?>
                                                        <?php echo $library['name'] . ": " . $library['status'] . "<br />"; ?>
                                                    <?php endforeach; ?>

                                                    <?php foreach ($is_writable as $folder) : ?>
                                                        <?php echo $folder['name'] . ": " . $folder['status'] . "<br />"; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </b>
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="text-center">Create an admin user</h3>

                                    <div class="pt-2">
                                        <div class="form-label-group">
                                            <input class="form-control" type="text" maxlength="15" id="username" name="username" placeholder="Username" required>
                                            <small id="user_error" class="text-danger"></small>
                                        </div>
                                    </div>

                                    <div class="pt-3">
                                        <div class="form-label-group">
                                            <input class="form-control" type="password" minlength="8" id="password" name="password" placeholder="Password" required>
                                            <small id="password_error" class="text-danger"></small>
                                        </div>
                                    </div>

                                    <div class="pt-3">
                                        <div class="form-label-group">
                                            <input class="form-control" type="email" id="email" name="email" placeholder="Email Address" required>
                                            <small id="email_error" class="text-danger"></small>
                                        </div>
                                    </div>

                                    <div class="pt-3">
                                        <button type="submit" class="btn btn-primary btn-block" <?php echo $disabled; ?>>
                                            Start Installation
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

    <footer class="py-5 my-sm-10 bg-primary">
        <div class="container-fluid my-auto">
            <p class="m-0 text-center text-white">
                Copyright &copy; Uploady - <?= date('Y') ?>
            </p>
        </div>
    </footer>

    <?php include_once 'components/js.php'; ?>
</body>

</html>

</html>