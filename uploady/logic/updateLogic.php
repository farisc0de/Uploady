<?php

use Uploady\Migration\Options\Options;
use Uploady\Migration\Options\Types;

$utils = new Uploady\Utils();

$database = new Uploady\Database();

$update = new Uploady\Migration\Migration($database, $utils);

$upload = new Farisc0de\PhpFileUploading\Upload($utils);

$update->insertValue(
    "settings",
    [
        'id' => 18,
        'setting_key' => 'website_headline',
        'setting_value' => 'Simple File Uploading Software'
    ]
);

$update->insertValue(
    "settings",
    [
        'id' => 16,
        'setting_key' => 'adsense_status',
        'setting_value' =>  0
    ]
);

$update->insertValue(
    "settings",
    [
        'id' => 17,
        'setting_key' => 'adsense_client_code',
        'setting_value' =>  ''
    ]
);

$update->deleteValue("settings", "id", 6);

$pages = [
    ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
    ["slug", Types::Text(), Options::NotNull()],
    ["title", Types::Text(), Options::NotNull()],
    ["content", Types::LongText(), Options::NotNull()],
    ["deletable", Types::Boolean(), Options::DefaultValue(0), Options::NotNull()],
    ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp(), Options::NotNull()]
];

$update->createTable("pages", $pages);

$update->insertValue("pages", [
    'id' => 1,
    'slug' => 'about',
    'title' => 'About us',
    'content' => file_get_contents(realpath(APP_PATH . 'pages/about.html')),
    'deletable' => false
]);

$update->insertValue("pages", [
    'id' => 2,
    'slug' => 'terms',
    'title' => 'Terms of services',
    'content' => file_get_contents(realpath(APP_PATH . 'pages/terms.html')),
    'deletable' => false
]);

$update->insertValue("pages", [
    'id' => 3,
    'slug' => 'privacy',
    'title' => 'Privacy Policy',
    'content' => file_get_contents(realpath(APP_PATH . 'pages/privacy.html')),
    'deletable' => false
]);


$update->setPrimary("pages", "id");

$update->setAutoinc("pages", [
    "id",
    Types::Integer(),
    Options::UnSigned(),
    Options::NotNull()
]);
