<?php

use Uploady\Migration\Options\Options;
use Uploady\Migration\Options\Types;

$utils = new Uploady\Utils();

$database = new Uploady\Database();

$install = new Uploady\Migration\Migration($database, $utils);

$upload = new Farisc0de\PhpFileUploading\Upload();

$php_alert =  "";

if (PHP_VERSION_ID < 70200) {
    $php_alert = $utils->alert("Please update your PHP to 7.2", "danger", "times-circle");
}

$required_libs = [
    "JSON" => "json",
    "PDO" => "pdo",
    "MySQL" => "pdo_mysql",
    "Mbstring" => "mbstring",
];

$is_installed = [];

foreach ($required_libs as $lib_name => $lib_id) {
    if (extension_loaded($lib_id) == true) {
        array_push($is_installed, ["name" => $lib_name, "status" => "Installed", "bool" => true]);
    } else {
        array_push($is_installed, ["name" => $lib_name, "status" => "Missing", "bool" => false]);
    }
}

$writables = [
    "uploads",
    "config/config.php",
    "config/environment.php"
];

$is_writable = [];

foreach ($writables as $file_name) {
    if (is_writable($file_name) == true) {
        array_push($is_writable, [
            "name" => $file_name,
            "status" => "Writable",
            "bool" => true
        ]);
    } else {
        array_push($is_writable, [
            "name" => $file_name,
            "status" => "Not Writable",
            "bool" => false
        ]);
    }
}

$disabled = "";

if (
    $utils->findKeyValue($is_installed, "bool", false) ||
    $utils->findKeyValue($is_writable, "bool", false) ||
    PHP_VERSION_ID < 70200
) {
    $disabled = "disabled";
}

$upload->generateUserID();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $users = [
            [
                'id',
                Types::Integer(),
                Options::UnSigned(),
                Options::NotNull()
            ],
            [
                'username', Types::String(25),
                Options::NotNull()
            ],
            [
                'email',
                Types::String(225),
                Options::NotNull()
            ],
            [
                'password',
                Types::String(225),
                Options::NotNull()
            ],
            [
                'user_id',
                Types::String(64),
                Options::NotNull()
            ],
            [
                'role',
                Types::Integer(),
                Options::NotNull(),
                Options::DefaultValue("1")
            ],
            [
                'otp_status',
                Types::Boolean(),
                Options::NotNull(),
                Options::DefaultValue("0")
            ],
            [
                'otp_secret',
                Types::String(255),
                Options::Null(),
            ],
            [
                'failed_login',
                Types::Integer(),
                Options::NotNull(),
                Options::DefaultValue("0")
            ],
            [
                'last_login',
                Types::TimeStamp(),
                Options::NotNull(),
                Options::DefaultValue("CURRENT_TIMESTAMP")
            ],
            [
                'reset_hash',
                Types::String(64),
                Options::DefaultValue("NULL")
            ],
            [
                'created_at',
                Types::TimeStamp(),
                Options::Null(),
                Options::DefaultValue("NULL")
            ],
            [
                'activation_hash',
                Types::String(64),
                Options::DefaultValue("NULL")
            ],
            [
                'is_active',
                Types::Boolean(),
                Options::NotNull(),
                Options::DefaultValue("0")
            ]
        ];

        $files = [
            ['id', Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ['file_id', Types::String(100), Options::NotNull()],
            ['user_id', Types::String(100), Options::NotNull()],
            ['file_data', Types::LongText(), Options::NotNull()],
            ['users_data', Types::LongText(), Options::NotNull()],
            ['downloads', Types::Integer(), Options::NotNull()],
            ['uploaded_at', Types::TimeStamp(), Options::NotNull()]
        ];

        $settings = [
            ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ["setting_key", Types::String(50), Options::NotNull()],
            ["setting_value", Types::String(225), Options::NotNull()],
        ];

        $pages = [
            ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ["slug", Types::Text(), Options::NotNull()],
            ["deletable", Types::Boolean(), Options::DefaultValue(0), Options::NotNull()],
            ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp(), Options::NotNull()]
        ];

        $languages = [
            ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ["language", Types::String(50), Options::NotNull()],
            ["language_code", Types::String(50), Options::NotNull()],
            ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp(), Options::NotNull()]
        ];

        $pages_translation = [
            ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ["page_id", Types::Integer(), Options::NotNull()],
            ["language_id", Types::Integer(), Options::NotNull()],
            ["title", Types::Text(), Options::NotNull()],
            ["content", Types::LongText(), Options::NotNull()],
            ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp(), Options::NotNull()]
        ];

        $roles = [
            ["id", Types::Integer(), Options::UnSigned(), Options::NotNull()],
            ["title", Types::String(75), Options::NotNull()],
            ["size_limit", Types::String(150), Options::NotNull()],
            ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp(), Options::NotNull()]
        ];

        $install->createTable("users", $users);

        $install->createTable("files", $files);

        $install->createTable("settings", $settings);

        $install->createTable("pages", $pages);

        $install->createTable("pages_translation", $pages_translation);

        $install->createTable("roles", $roles);

        $install->createTable("languages", $languages);

        $install->insertValue("users", [
            "id" => 1,
            "username" => $utils->sanitize($_POST["username"]),
            "email" => $utils->sanitize($_POST["email"]),
            "password" => password_hash($utils->sanitize($_POST["password"]), PASSWORD_BCRYPT),
            "user_id" => $upload->getUserID(),
            "role" => 3,
            "is_active" => 1
        ]);

        $install->insertValue(
            "settings",
            [
                'id' => 1,
                'setting_key' => 'website_name',
                'setting_value' => 'Uploady'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'id' => 2,
                'setting_key' => 'website_headline',
                'setting_value' => 'Simple File Uploading Software'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'id' => 3,
                'setting_key' => 'description',
                'setting_value' => 'this is uploading service website'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'id' => 4,
                'setting_key' => 'keywords',
                'setting_value' => 'upload,file upload,file uploading,file sharing'
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 5,
                'setting_key' => 'owner_name',
                'setting_value' => $utils->sanitize($_POST['username'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 6,
                'setting_key' => 'owner_email',
                'setting_value' => $utils->sanitize($_POST['email'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 7,
                'setting_key' => 'smtp_status',
                'setting_value' => '0'
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 8,
                'setting_key' => 'smtp_host',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 9,
                'setting_key' => 'smtp_username',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 10,
                'setting_key' => 'smtp_password',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 11,
                'setting_key' => 'smtp_port',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 12,
                'setting_key' => 'smtp_security',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 13,
                'setting_key' => 'recaptcha_status',
                'setting_value' => '0'
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 14,
                'setting_key' => 'recaptcha_site_key',
                'setting_value' =>  ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 15,
                'setting_key' => 'recaptcha_secret_key',
                'setting_value' =>  ''
            ]
        );

        $install->insertValue(
            "settings",
            [
                'id' => 16,
                'setting_key' => 'adsense_status',
                'setting_value' =>  0
            ]
        );

        $install->insertValue(
            "settings",
            [
                'id' => 17,
                'setting_key' => 'adsense_client_code',
                'setting_value' =>  ''
            ]
        );


        $install->insertValue("pages", [
            'id' => 1,
            'slug' => 'about',
            'deletable' => false
        ]);

        $install->insertValue("pages", [
            'id' => 2,
            'slug' => 'terms',
            'deletable' => false
        ]);

        $install->insertValue("pages", [
            'id' => 3,
            'slug' => 'privacy',
            'deletable' => false
        ]);

        $install->insertValue("roles", [
            'id' => 1,
            'title' => 'User',
            'size_limit' => '150 MB',
        ]);


        $install->insertValue("roles", [
            'id' => 2,
            'title' => 'Guest',
            'size_limit' => '50 MB',
        ]);

        $install->insertValue("roles", [
            'id' => 3,
            'title' => 'Admin',
            'size_limit' => '500 MB',
        ]);

        $install->setPrimary("users", "id");

        $install->setUnique("users", "email");

        $install->setUnique("users", "user_id");

        $install->setUnique("users", "activation_hash");

        $install->setAutoinc("users", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("files", "id");

        $install->setUnique("files", "file_id");

        $install->setAutoinc("files", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("settings", "id");

        $install->setAutoinc("settings", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("pages", "id");

        $install->setAutoinc("pages", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("roles", "id");

        $install->setAutoinc("roles", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("languages", "id");

        $install->setAutoinc("languages", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        $install->setPrimary("pages_translation", "id");

        $install->setAutoinc("pages_translation", [
            "id",
            Types::Integer(),
            Options::UnSigned(),
            Options::NotNull()
        ]);

        // Enable Production Mode
        /* -------------------------- */
        $env_file = APP_PATH . "config/environment.php";

        $env_file_content = file_get_contents($env_file);

        $env_file_content = preg_replace("/installation/", "production", $env_file_content, 1);

        file_put_contents($env_file, $env_file_content);
        /* -------------------------- */
        $msg = true;
    } catch (PDOException $ex) {
        $error = $ex->getMessage();
        error_log($ex->getMessage() . "\n", 3, LOGS_PATH);
    }
}

$page = "installPage";
