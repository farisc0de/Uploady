<?php

session_start();

require_once dirname(__FILE__, 2) . '/config/config.php';

$db = new Uploady\Database();

$utils = new Uploady\Utils();

$user = new Uploady\User($db, $utils);

$auth = new Uploady\Auth($db, $utils, $user);

$settings = new Uploady\Settings($db);

$st = $settings->getSettings();

if (isset($_SESSION)) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

    if (!isset($_SESSION['loggedin'])) {
        $utils->redirect($utils->siteUrl("/login.php"));
    }

    if ($username != null) {
        $data = $user->get($username);

        if (!isset($_SESSION['current_ip'])) {
            $_SESSION['current_ip'] = $utils->sanitize($_SERVER['REMOTE_ADDR']);
        }

        if (!(isset($_SESSION['csrf']))) {
            $auth->generateSessionToken();
        }

        if ($user->isTwoFAEnabled($username) == true) {
            if (!isset($_SESSION['OTP']) || $_SESSION['OTP'] != true) {
                if (!strpos($current_url, "auth.php")) {
                    $utils->redirect($utils->siteUrl("/auth.php"));
                }
            }
        }

        if (isset($_SESSION['isHuman'])) {
            if ($_SESSION['isHuman'] == false) {
                $utils->redirect($utils->siteUrl('/logout.php'));
            }
        }

        if ($data->role != 3) {
            $utils->redirect($utils->siteUrl('/index.php'));
        }
    } else {
        $utils->redirect($utils->siteUrl("/login.php"));
    }
}
