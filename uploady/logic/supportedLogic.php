<?php
$filter = json_decode(
    file_get_contents("vendor/farisc0de/phpfileuploading/src/filter.json"),
    true
);

$page = 'supported';
$title = $lang["general"]['supported_formats'];
