<?php

$utils = new Uploady\Utils();

$database = new Uploady\Database();

$install = new Uploady\Update($database, $utils);

$upload = new \Uploady\Handler\Upload();

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

$writable_folders = ["uploads"];

$is_writable = [];

foreach ($writable_folders as $folder_name) {
    if (is_writable($folder_name) == true) {
        array_push($is_writable, [
            "name" => $folder_name,
            "status" => "Writable",
            "bool" => true
        ]);
    } else {
        array_push($is_writable, [
            "name" => $folder_name,
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $users = [
            ['id', 'int', 'UNSIGNED', 'NOT NULL'],
            ['username', 'varchar(25)', 'NOT NULL'],
            ['email', 'varchar(225)', 'NOT NULL'],
            ["password", "varchar(255)", "NOT NULL"],
            ['user_id', 'varchar(64)', 'NOT NULL'],
            ['is_admin', 'tinyint(1)', 'NOT NULL', 'DEFAULT 0'],
            ['failed_login', 'int', 'NOT NULL', 'DEFAULT 0'],
            ['last_login', 'timestamp', 'NOT NULL', 'DEFAULT CURRENT_TIMESTAMP'],
            ['reset_hash', 'varchar(64)', 'DEFAULT NULL'],
            ['created_at', 'timestamp', 'NULL', 'DEFAULT NULL'],
            ['activation_hash', 'varchar(64)', 'DEFAULT NULL'],
            ['is_active', 'tinyint(1)', 'NOT NULL', 'DEFAULT 0']
        ];

        $files = [
            ['id', 'int', 'NOT NULL'],
            ['file_id', 'varchar(100)', 'NOT NULL'],
            ['user_id', 'varchar(100)', 'NOT NULL'],
            ['file_data', 'text', 'NOT NULL'],
            ['uploaded_at', 'timestamp', 'NOT NULL']
        ];

        $settings = [
            ["id", "int(11)", "unsigned", "NOT NULL"],
            ["setting_key", "varchar(50)", "NOT NULL"],
            ["setting_value", "varchar(225)", "NOT NULL"],
        ];

        $install->createTable("users", $users);

        $install->createTable("files", $files);

        $install->createTable("settings", $settings);

        $install->insertValue("users", [
            "id" => 1,
            "username" => $utils->sanitize($_POST["username"]),
            "email" => $utils->sanitize($_POST["email"]),
            "password" => password_hash($utils->sanitize($_POST["password"]), PASSWORD_BCRYPT),
            "user_id" => $upload->generateUserID(),
            "is_admin" => 1,
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
                'setting_key' => 'description',
                'setting_value' => 'this is uploading service website'
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 3,
                'setting_key' => 'keywords',
                'setting_value' => 'upload,file upload,file uploading,file sharing'
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 4,
                'setting_key' => 'owner_name',
                'setting_value' => $utils->sanitize($_POST['username'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 5,
                'setting_key' => 'owner_email',
                'setting_value' => $utils->sanitize($_POST['email'])
            ]
        );
        $install->insertValue(
            "settings",
            [
                'id' => 6,
                'setting_key' => 'theme_name',
                'setting_value' => 'litera'
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

        $install->isPrimary("users", "id");

        $install->isUnique("users", "email");

        $install->isUnique("users", "user_id");

        $install->isUnique("users", "activation_hash");

        $install->isAutoinc("users", ["id", "int(11)", "unsigned", "NOT NULL"]);

        $install->isPrimary("files", "id");

        $install->isUnique("files", "file_id");

        $install->isAutoinc("files", ["id", "int(11)", "unsigned", "NOT NULL"]);

        $install->isPrimary("settings", "id");

        $install->isAutoinc("settings", ["id", "int(11)", "unsigned", "NOT NULL"]);

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
