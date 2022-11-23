<?php

header("Content-Type: application/json");
require_once '../../config/config.php';

$database = new Uploady\Database();

$utils = new Uploady\Utils();

$uploads = new Uploady\Handler\UploadHandler($database);

$count = [];

$temp = [];

if ($uploads->countFiles() > 0) {
    foreach ($uploads->getFiles() as $dd) {
        if (date("Y", strtotime($dd->uploaded_at)) == date("Y")) {
            $month = date("m", strtotime($dd->uploaded_at));

            if (!key_exists($month, $temp)) {
                $temp[$month] = 0;
            }

            $temp[$month] += 1;
        }
    }

    foreach ($temp as $key => $value) {
        array_push($count, [
            'label' => date('F', mktime(0, 0, 0, $key, 10)),
            'data' => $value
        ]);
    }
} else {
    array_push($count, ["label" => "Nothing", "data" => 0]);
}

echo json_encode($count);
