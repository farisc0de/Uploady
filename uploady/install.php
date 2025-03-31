<?php
include_once 'config/config.php';
include_once APP_PATH . 'logic/installLogic.php';
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <?php
    $utils->style('css/main.css');
    ?>
</head>

<body class="d-flex flex-column h-100">

    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= $utils->siteUrl(); ?>">
                <i class="fas fa-cloud-upload-alt me-2"></i> Uploady
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <!-- Installation Header -->
                <div class="text-center mb-4">
                    <h1 class="display-6 mb-3">Welcome to Uploady</h1>
                    <p class="lead">Complete the installation to start using your file sharing platform</p>
                </div>
                
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Software Installation
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <?php if (isset($msg)) : ?>
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <div class="me-3">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Success!</h5>
                                        <p class="mb-0">Script has been installed successfully.</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($error)) : ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <div class="me-3">
                                        <i class="fas fa-times-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Error</h5>
                                        <p class="mb-0"><?= $error ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!isset($_POST['install'])) : ?>
                                <?php echo $php_alert; ?>

                                <div class="alert alert-info mb-4">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-2x"></i>
                                        </div>
                                        <div class="text-start">
                                            <h5 class="alert-heading">System Requirements</h5>
                                            <div>
                                                <p class="mb-2"><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                                                <ul class="list-group mb-3">
                                                    <?php foreach ($is_installed as $library) : ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <?= $library['name'] ?>
                                                            <?php if (strpos($library['status'], 'Installed') !== false) : ?>
                                                                <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                            <?php else : ?>
                                                                <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                
                                                <p class="mb-2"><strong>Directory Permissions:</strong></p>
                                                <ul class="list-group">
                                                    <?php foreach ($is_writable as $folder) : ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <?= $folder['name'] ?>
                                                            <?php if (strpos($folder['status'], 'Writable') !== false) : ?>
                                                                <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                            <?php else : ?>
                                                                <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <h5 class="mb-3">Admin Account Setup</h5>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input class="form-control" type="text" maxlength="15" id="username" name="username" placeholder="Enter admin username" required>
                                </div>
                                <small id="user_error" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input class="form-control" type="password" minlength="8" id="password" name="password" placeholder="Enter secure password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password must be at least 8 characters long</div>
                                <small id="password_error" class="text-danger"></small>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input class="form-control" type="email" id="email" name="email" placeholder="Enter admin email" required>
                                </div>
                                <small id="email_error" class="text-danger"></small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="install" class="btn btn-primary" <?php echo $disabled; ?>>
                                    <i class="fas fa-cog me-2"></i>Start Installation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer py-4 bg-body-tertiary mt-auto">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0">
                    <i class="fas fa-cloud-upload-alt me-1"></i> Uploady
                </p>
                <p class="mb-0">
                    Copyright &copy; <?= date('Y') ?>
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>

</html>