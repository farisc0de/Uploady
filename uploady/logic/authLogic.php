<?php

$page = "authPage";

$utils = new Uploady\Utils();
$database = new Uploady\Database();
$user = new Uploady\User($database, $utils);
$auth = new Uploady\Auth($database, $utils, $user);

// Check if the user is loggedin
if (!isset($_SESSION['loggedin'])) {
    $utils->redirect($utils->siteUrl("/logout.php"));
} elseif (isset($_SESSION['OTP']) && ($_SESSION['OTP'] == true)) {
    $utils->redirect($utils->siteUrl("/index.php"));
} else {
    $_SESSION['OTP'] = false;
}

$uniqueid = $auth->generateDeviceID();

if ($auth->checkDeviceId($uniqueid) == true) {
    $user->regenerateSession();
}

$g = new \RobThree\Auth\TwoFactorAuth("Uploady");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $utils->sanitize($_POST['otp_code']);
    $secret = $user->getSecret($_SESSION['username']);

    if ($g->verifyCode($secret, $code)) {
        if (isset($_POST['remberme'])) {
            if (!isset($_COOKIE['2fa'])) {
                $utils->createCookie("2fa", true);
                $utils->createCookie("device_id", $uniqueid);
            }
        }

        $user->regenerateSession();
    } else {
        $error = $lang["general"]["two_factor_auth_failed"];
    }
}

$title = $lang["general"]['two_factor_title'];
