<?php
header("Content-Type: application/json");
require_once '../session.php';

$counter = new Uploady\Handler\UploadHandler($db, $utils);

$files = $counter->getFiles();

$countries = $counter->getCountries();
$arrays = [];

$uploaded_files = [];
$count = [];

foreach ($files as $file) {
    $f = json_decode($file->user_data);
    array_push($arrays, $f);
}

print_r($uploaded_files);

foreach ($uploaded_files as $data => $value) {
    if (array_key_exists($data, $count)) {
        $count[$data] = $count[$data] + 1;
    } else {
        array_push(
            $arrays,
            [
                "id" => $data['country'],
                "value" => 1
            ]
        );
    }
}

echo json_encode(["countries" => $arrays]);
