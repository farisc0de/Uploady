<?php

$auth = new \RobThree\Auth\TwoFactorAuth("Uploady");

$secret = $auth->createSecret();

$title = $lang["general"]["enable_two_factor_title"];
