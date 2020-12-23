<?php

session_start();
include_once 'config/config.php';

$utils = new Uploady\Utils();

if (session_destroy()) {
    $utils->redirect($utils->siteUrl('/index.php'));
}
