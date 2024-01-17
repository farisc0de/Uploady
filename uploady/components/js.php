<?php

$utils->script("https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js");
$utils->script("https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js");
$utils->script("https://code.jquery.com/jquery-3.6.0.min.js");
$utils->script("https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js");
$utils->script("https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js");
$utils->script("https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js");
$utils->script("https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js");

if (strpos($_SERVER['REQUEST_URI'], 'edit.php') !== false) {
    $utils->script("vendor/camansjs/dist/caman.full.min.js");
    $utils->script("js/editor.js");
}

$utils->script("js/main.js");
$utils->script("js/custom.js");

if (isset($page) && $page == 'index') {
    $utils->script("js/upload.js");
}

include_once APP_PATH . "modules/adsense/main.php";
include_once APP_PATH . "modules/analytics/main.php";
include_once APP_PATH . "modules/analytics/main.php";
