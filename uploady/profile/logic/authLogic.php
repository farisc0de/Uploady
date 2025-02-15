<?php

$auth = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\BaconQrCodeProvider(), "Uploady");

$secret = $auth->createSecret();

$title = $lang["general"]["enable_two_factor_title"];
