<?php

use Farisc0de\PhpFileUploading\Utility;

$utility = new Utility();

$allowedExtensions = $utility->returnCommaSeparatedExtensions();

$page = 'index';
$title = $st['website_headline'];
