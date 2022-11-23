<?php

$auth = new \RobThree\Auth\TwoFactorAuth("Uploady");

$secret = $auth->createSecret();
