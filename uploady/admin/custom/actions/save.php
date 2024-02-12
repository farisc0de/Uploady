<?php

include_once '../../session.php';

if ($_POST['button'] == "css") {
    file_put_contents(APP_PATH . "/assets/css/custom.css", $_POST['editor']);
}

if ($_POST['button'] == "js") {
    file_put_contents(APP_PATH . "/assets/js/custom.js", $_POST['editor']);
}

echo "success";
