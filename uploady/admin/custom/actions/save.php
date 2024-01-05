<?php

include_once '../../session.php';

if ($_POST['css'] != null) {
    file_put_contents(APP_PATH . "/assets/css/custom.css", $_POST['css']);
}

if ($_POST['js'] != null) {
    file_put_contents(APP_PATH . "/assets/js/custom.js", $_POST['js']);
}

echo "success";
