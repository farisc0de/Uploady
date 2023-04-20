<?php
header("Content-Type: application/json");
require_once '../session.php';

$counter = new Uploady\Handler\UploadHandler($db, $utils);

$files = $counter->getFiles();

$countries = $counter->getCountries();
$arrays = [];

$list = [];

$uploaded_files = [];

foreach ($files as $file) {
    $f = json_decode($file->user_data, true);
    array_push($arrays, $f);
}

foreach ($countries as $country_code => $country) {
    array_push($list, [
        "id" => $country_code,
        "value" => 0,
    ]);
}

foreach ($arrays as $array) {
    foreach ($array as $key => $value) {
        if ($key == "country") {
            foreach ($list as $key => $country) {
                if ($country["id"] == $value) {
                    $list[$key]["value"]++;
                }
            }
        }
    }
}

echo json_encode(["countries" => $list]);
