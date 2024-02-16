<?php

if (ENVIRONMENT == 'production' || ENVIRONMENT == 'testing' || ENVIRONMENT == 'development') {
    header("Location: /");
    exit;
}

use Farisc0de\PhpMigration\Options\Options;
use Farisc0de\PhpMigration\Options\Types;

$utils = new Uploady\Utils();

$database = new Uploady\Database();

$install = new \Farisc0de\PhpMigration\Migration($database, $utils);

$upload = new Farisc0de\PhpFileUploading\Upload(new Farisc0de\PhpFileUploading\Utility());

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
    array_push($is_installed, ["name" => $lib_name, "status" => extension_loaded($lib_id) ? "Installed" : "Missing"]);
}

$writables = [
    "uploads",
    "config/config.php",
    "config/environment.php"
];

$is_writable = [];

foreach ($writables as $file_name) {
    array_push($is_writable, [
        "name" => $file_name,
        "status" => is_writable($file_name) == true ? "Writable" : "Not Writable",
    ]);
}

$disabled = "";

if (
    $utils->findKeyValue($is_installed, "status", "Missing") ||
    $utils->findKeyValue($is_writable, "status", "Not Writable") ||
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
                Types::integer(),
                Options::unSigned(),
                Options::notNull()
            ],
            [
                'username', Types::string(25),
                Options::notNull()
            ],
            [
                'email',
                Types::string(225),
                Options::notNull()
            ],
            [
                'password',
                Types::string(225),
                Options::notNull()
            ],
            [
                'user_id',
                Types::string(64),
                Options::notNull()
            ],
            [
                'role',
                Types::integer(),
                Options::notNull(),
                Options::defaultValue("1")
            ],
            [
                'api_key',
                Types::string(255),
                Options::notNull(),
                Options::defaultValue(bin2hex(random_bytes(16)))
            ],
            [
                'otp_status',
                Types::Boolean(),
                Options::notNull(),
                Options::defaultValue("0")
            ],
            [
                'otp_secret',
                Types::string(255),
                Options::Null(),
            ],
            [
                'failed_login',
                Types::integer(),
                Options::notNull(),
                Options::defaultValue("0")
            ],
            [
                'last_login',
                Types::timeStamp(),
                Options::notNull(),
                Options::defaultValue("CURRENT_TIMESTAMP")
            ],
            [
                'reset_hash',
                Types::string(64),
                Options::Null(),
            ],
            [
                'created_at',
                Types::timeStamp(),
                Options::Null(),
            ],
            [
                'activation_hash',
                Types::string(64),
                Options::Null(),
            ],
            [
                'is_active',
                Types::Boolean(),
                Options::notNull(),
                Options::defaultValue("0")
            ]
        ];

        $files = [
            ['id', Types::integer(), Options::unSigned(), Options::notNull()],
            ['file_id', Types::string(100), Options::notNull()],
            ['user_id', Types::string(100), Options::notNull()],
            ['file_data', Types::LongText(), Options::notNull()],
            ['file_settings', Types::LongText(), Options::notNull()],
            ['user_data', Types::LongText(), Options::notNull()],
            ['downloads', Types::integer(), Options::null()],
            ['uploaded_at', Types::timeStamp(), Options::null()],
        ];

        $settings = [
            ["id", Types::integer(), Options::unSigned(), Options::notNull()],
            ["setting_key", Types::string(50), Options::notNull()],
            ["setting_value", Types::string(225)],
        ];

        $pages = [
            ["id", Types::integer(), Options::unSigned(), Options::notNull()],
            ["slug", Types::Text(), Options::notNull()],
            ["deletable", Types::Boolean(), Options::defaultValue(0), Options::notNull()],
            ["created_at", Types::timeStamp(), Options::currentTimeStamp(), Options::notNull()]
        ];

        $languages = [
            ["id", Types::integer(), Options::unSigned(), Options::notNull()],
            ["language", Types::string(50), Options::notNull()],
            ["language_code", Types::string(50), Options::notNull()],
            ["language_direction", Types::string(10), Options::defaultValue("ltr"), Options::notNull()],
            ["is_active", Types::boolean(), Options::defaultValue(0), Options::notNull()],
            ["created_at", Types::timeStamp(), Options::currentTimeStamp(), Options::notNull()]
        ];

        $pages_translation = [
            ["id", Types::integer(), Options::unSigned(), Options::notNull()],
            ["page_id", Types::integer(), Options::notNull()],
            ["language_id", Types::integer(), Options::notNull()],
            ["title", Types::Text(), Options::notNull()],
            ["content", Types::LongText(), Options::notNull()],
            ["created_at", Types::timeStamp(), Options::currentTimeStamp(), Options::notNull()]
        ];

        $roles = [
            ["id", Types::integer(), Options::unSigned(), Options::notNull()],
            ["title", Types::string(75), Options::notNull()],
            ["size_limit", Types::string(150), Options::notNull()],
            ["created_at", Types::timeStamp(), Options::currentTimeStamp(), Options::notNull()]
        ];

        $install->createTable("users", $users);

        $install->createTable("files", $files);

        $install->createTable("settings", $settings);

        $install->createTable("pages", $pages);

        $install->createTable("pages_translation", $pages_translation);

        $install->createTable("roles", $roles);

        $install->createTable("languages", $languages);

        $install->setPrimary("users", "id");

        $install->setUnique("users", "email");

        $install->setUnique("users", "user_id");

        $install->setUnique("users", "activation_hash");

        $install->setAutoinc("users", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("files", "id");

        $install->setUnique("files", "file_id");

        $install->setAutoinc("files", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("settings", "id");

        $install->setAutoinc("settings", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("pages", "id");

        $install->setAutoinc("pages", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("roles", "id");

        $install->setAutoinc("roles", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("languages", "id");

        $install->setAutoinc("languages", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);

        $install->setPrimary("pages_translation", "id");

        $install->setAutoinc("pages_translation", [
            "id",
            Types::integer(),
            Options::unSigned(),
            Options::notNull()
        ]);


        $install->insertValue("users", [
            "username" => $utils->sanitize($_POST["username"]),
            "email" => $utils->sanitize($_POST["email"]),
            "password" => password_hash($utils->sanitize($_POST["password"]), PASSWORD_BCRYPT),
            "user_id" => $upload->getUserID(),
            "role" => 3,
            "api_key" => bin2hex(random_bytes(16)),
            "is_active" => true
        ]);

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'website_name',
                'setting_value' => 'Uploady'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'website_headline',
                'setting_value' => 'Simple File Uploading Software'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'description',
                'setting_value' => 'this is uploading service website'
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'keywords',
                'setting_value' => 'upload,file upload,file uploading,file sharing'
            ]
        );


        $install->insertValue(
            "settings",
            [
                'setting_key' => 'website_logo',
                'setting_value' => null
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'website_favicon',
                'setting_value' => null
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'owner_name',
                'setting_value' => $utils->sanitize($_POST['username'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'owner_email',
                'setting_value' => $utils->sanitize($_POST['email'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'public_upload',
                'setting_value' => false
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'twitter_link',
                'setting_value' => null
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'instagram_link',
                'setting_value' => null
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'linkedin_link',
                'setting_value' => null
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_status',
                'setting_value' => false
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_host',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_username',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_password',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_port',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'smtp_security',
                'setting_value' => ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => false
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'recaptcha_status',
                'setting_value' => false
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'recaptcha_site_key',
                'setting_value' =>  ''
            ]
        );
        $install->insertValue(
            "settings",
            [
                'setting_key' => 'recaptcha_secret_key',
                'setting_value' =>  ''
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'adsense_status',
                'setting_value' =>  false
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'adsense_client_code',
                'setting_value' =>  ''
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'analytics_status',
                'setting_value' =>  false
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'analytics_code',
                'setting_value' =>  ''
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'sharethis_status',
                'setting_value' =>  false
            ]
        );

        $install->insertValue(
            "settings",
            [
                'setting_key' => 'sharethis_code',
                'setting_value' =>  ''
            ]
        );

        $install->insertValue("pages", [
            'slug' => 'about',
            'deletable' => false
        ]);

        $install->insertValue("pages", [
            'slug' => 'terms',
            'deletable' => false
        ]);

        $install->insertValue("pages", [
            'slug' => 'privacy',
            'deletable' => false
        ]);

        $install->insertValue("roles", [
            'title' => 'User',
            'size_limit' => '150 MB',
        ]);


        $install->insertValue("roles", [
            'title' => 'Guest',
            'size_limit' => '50 MB',
        ]);

        $install->insertValue("roles", [
            'title' => 'Admin',
            'size_limit' => '500 MB',
        ]);

        foreach ($utils->getLanguages() as $code => $name) {
            $install->insertValue("languages", [
                'language' => $name,
                'language_code' => $code,
                'is_active' => $code == 'en' ? true : false,
            ]);
        }

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
