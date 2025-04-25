<?php

$auth = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider(), "Uploady");

$secret = $auth->createSecret();

$title = $lang["general"]["enable_two_factor_title"];
