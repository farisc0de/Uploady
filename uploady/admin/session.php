<?php

session_start();
require_once dirname(__FILE__, 2) . '/config/config.php';

$db = new Uploady\Database();

$utils = new Uploady\Utils();

$user = new Uploady\User($db);

$auth = new Uploady\Auth($db, $utils);

$settings = new Uploady\Settings($db);

$st = $settings->getSettings();

if (isset($_SESSION)) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

    if (!isset($_SESSION['loggedin'])) {
        $utils->redirect($utils->siteUrl("/login.php"));
    }

    if ($username != null) {
        $data = $user->getUserData($username);

        if (!isset($_SESSION['current_ip'])) {
            $_SESSION['current_ip'] = $utils->sanitize($_SERVER['REMOTE_ADDR']);
        }

        if (!(isset($_SESSION['csrf']))) {
            $auth->generateSessionToken();
        }

        if (isset($_SESSION['isHuman'])) {
            if ($_SESSION['isHuman'] == false) {
                $utils->redirect($utils->siteUrl('/logout.php'));
            }
        }

        if (!$data->is_admin) {
            $utils->redirect($utils->siteUrl('/index.php'));
        }
    } else {
        $utils->redirect($utils->siteUrl("/login.php"));
    }
}
