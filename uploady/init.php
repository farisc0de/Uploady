<?php

$db = new Uploady\Database();
$utils = new Uploady\Utils();
$user = new Uploady\User($db, $utils);
$auth = new Uploady\Auth($db, $utils, $user);
$settings = new Uploady\Settings($db);
$localization = new Uploady\Localization($db);
$role = new Uploady\Role($db, $user);


if (ENVIRONMENT != "installation") {
    $st = $settings->getSettings();

    if (ENVIRONMENT == 'development' || ENVIRONMENT == 'testing') {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    if ($st['maintenance_mode'] == 1) {
        if (!strpos($_SERVER['REQUEST_URI'], "maintenance.php")) {
            $utils->redirect($utils->siteUrl('/maintenance.php'));
        }
    }

    $allowed_pages = array(
        'login.php',
        'signup.php',
        'auth.php',
        'page.php',
        'download.php',
        'activate.php',
        'maintenance.php',
        'forgot-password.php',
        'reset.php',
        'supported.php',
        'reportabuse.php',
    );

    $current_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
