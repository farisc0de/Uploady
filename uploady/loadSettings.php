<?php

use Uploady\Database;
use Uploady\Settings;
use Uploady\Utils;

$db = new Database();

$settings = new Settings($db);

$utils = new Utils();

$st = $settings->getSettings();
