<?php

use Uploady\Localization;

$lang = new Localization($db);

$languages = $lang->getLanguages();

$msg = $_GET['msg'] ?? null;
