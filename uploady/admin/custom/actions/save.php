<?php

include_once '../../session.php';

if ($_POST['css'] == "") {
    echo "error";
    exit;
}

if ($_POST['js'] == "") {
    echo "error";
    exit;
}

file_put_contents(APP_PATH . "/assets/css/custom.css", $_POST['css']);
file_put_contents(APP_PATH . "/assets/js/custom.js", $_POST['js']);

echo "success";
