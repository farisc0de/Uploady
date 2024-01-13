<?php
include_once '../../session.php';

$otpauth = new \RobThree\Auth\TwoFactorAuth("Uploady");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $msg_code = "csrf";
    } else {
        if (isset($_POST['enable'])) {
            if ($otpauth->verifyCode($_POST['otp_secret'], $_POST['otp_code'])) {
                $user->update($_POST['id'], [
                    'otp_status' => true,
                    'otp_secret' => $_POST['otp_secret'],
                ]);
                $msg_code = "two_factor_enabled";
            } else {
                $msg_code = "err";
            }
        }

        if (isset($_POST['disable'])) {
            $user->update($_POST['id'], [
                'otp_status' => false,
                'otp_secret' => ''
            ]);
            $msg_code = "two_factor_disabled";
        }
    }

    $utils->redirect($utils->siteUrl("/profile/auth.php?msg={$msg_code}"));
}
