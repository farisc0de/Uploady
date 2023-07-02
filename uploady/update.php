<?php
include_once 'config/config.php';
include_once APP_PATH . 'logic/updateLogic.php';
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <?php
    $utils->style('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css');
    $utils->style('css/main.css');
    ?>
</head>

<body class="d-flex flex-column h-100">

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $utils->siteUrl(); ?>">
                Uploady
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <div class="container pb-5 pt-5">
        <div class="row justify-content-center text-center">
            <div class="col-sm-12 col-md-8 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <b>Software Update</b>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if (isset($msg)) : ?>
                                <?php echo $utils->alert("Script has been updated.", "success", "check-circle"); ?>
                            <?php endif; ?>

                            <?php if (isset($error)) : ?>
                                <?php echo $utils->alert($error, "danger", "times-circle"); ?>
                            <?php endif; ?>

                            <div class="pt-3">
                                <button type="submit" class="btn btn-primary btn-block" <?php echo $disabled; ?>>
                                    Start Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer py-5 my-sm-10 bg-body-tertiary mt-auto">
        <div class="container-fluid my-auto">
            <p class="m-0 text-center">
                Copyright &copy; Uploady - <?= date('Y') ?>
            </p>
        </div>
    </footer>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">


</body>

</html>