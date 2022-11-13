<?php

$tfa = new RobThree\Auth\TwoFactorAuth('Uploady TwoFactorAuth');

if ($_POST) {
    if ($tfa->verifyCode($secret, $code) === true) {
    }
}
