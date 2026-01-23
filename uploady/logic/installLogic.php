<?php

/**
 * Installation Logic - Using New PHPMigration Library
 * 
 * This file handles the database installation using the fluent Schema Builder API
 */

session_start();

if (ENVIRONMENT == 'production' || ENVIRONMENT == 'testing' || ENVIRONMENT == 'development') {
    header("Location: /");
    exit;
}

use Farisc0de\PhpMigration\Database\Connection;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Grammars\MySqlGrammar;
use Uploady\Utils;

$utils = new Utils();

$php_alert = "";

if (PHP_VERSION_ID < 80100) {
    $php_alert = $utils->alert("Please update your PHP to 8.1 or higher", "danger", "times-circle");
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
    PHP_VERSION_ID < 80100
) {
    $disabled = "disabled";
}

$upload = new Farisc0de\PhpFileUploading\Upload(new Farisc0de\PhpFileUploading\Utility());
$upload->generateUserID();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Create database connection using new API
        $connection = Connection::create([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => 'utf8mb4',
        ]);

        // Create schema builder with MySQL grammar
        $grammar = new MySqlGrammar();
        $schema = new SchemaBuilder($connection, $grammar);

        // Create users table
        $schema->create('users', function ($table) {
            $table->id();
            $table->string('username', 25);
            $table->string('email', 225)->unique();
            $table->string('password', 225);
            $table->string('user_id', 64)->unique();
            $table->integer('role')->default(1);
            $table->string('api_key', 255)->default(bin2hex(random_bytes(16)));
            $table->boolean('otp_status')->default(false);
            $table->string('otp_secret', 255)->nullable();
            $table->integer('failed_login')->default(0);
            $table->timestamp('last_login')->useCurrent();
            $table->string('reset_hash', 64)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('activation_hash', 64)->nullable()->unique();
            $table->boolean('is_active')->default(false);
        });

        // Create files table
        $schema->create('files', function ($table) {
            $table->id();
            $table->string('file_id', 100)->unique();
            $table->string('user_id', 100);
            $table->longText('file_data');
            $table->longText('file_settings');
            $table->longText('user_data');
            $table->boolean('is_banned')->default(false);
            $table->integer('downloads')->nullable();
            $table->timestamp('uploaded_at')->nullable();
        });

        // Create settings table
        $schema->create('settings', function ($table) {
            $table->id();
            $table->string('setting_key', 50);
            $table->string('setting_value', 225)->nullable();
        });

        // Create pages table
        $schema->create('pages', function ($table) {
            $table->id();
            $table->text('slug');
            $table->boolean('deletable')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        // Create languages table
        $schema->create('languages', function ($table) {
            $table->id();
            $table->string('language', 50);
            $table->string('language_code', 50);
            $table->string('language_direction', 10)->default('ltr');
            $table->boolean('is_active')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        // Create pages_translation table
        $schema->create('pages_translation', function ($table) {
            $table->id();
            $table->integer('page_id');
            $table->integer('language_id');
            $table->text('title');
            $table->longText('content');
            $table->timestamp('created_at')->useCurrent();
        });

        // Create roles table
        $schema->create('roles', function ($table) {
            $table->id();
            $table->string('title', 75);
            $table->string('size_limit', 150);
            $table->timestamp('created_at')->useCurrent();
        });

        // Insert admin user
        $connection->prepare(
            "INSERT INTO users (username, email, password, user_id, role, api_key, is_active) 
             VALUES (:username, :email, :password, :user_id, :role, :api_key, :is_active)"
        );
        $connection->bind(':username', $utils->sanitize($_POST["username"]));
        $connection->bind(':email', $utils->sanitize($_POST["email"]));
        $connection->bind(':password', password_hash($utils->sanitize($_POST["password"]), PASSWORD_BCRYPT));
        $connection->bind(':user_id', $upload->getUserID());
        $connection->bind(':role', 3);
        $connection->bind(':api_key', bin2hex(random_bytes(16)));
        $connection->bind(':is_active', 1);
        $connection->execute();

        // Insert default settings
        $defaultSettings = [
            ['website_name', 'Uploady'],
            ['website_headline', 'Simple File Uploading Software'],
            ['description', 'this is uploading service website'],
            ['keywords', 'upload,file upload,file uploading,file sharing'],
            ['website_logo', null],
            ['website_favicon', null],
            ['owner_name', $utils->sanitize($_POST['username'])],
            ['owner_email', $utils->sanitize($_POST['email'])],
            ['virus_scanner', '0'],
            ['public_upload', '0'],
            ['disable_signup', '0'],
            ['twitter_link', null],
            ['instagram_link', null],
            ['linkedin_link', null],
            ['smtp_status', '0'],
            ['smtp_host', ''],
            ['smtp_username', ''],
            ['smtp_password', ''],
            ['smtp_port', ''],
            ['smtp_security', ''],
            ['maintenance_mode', '0'],
            ['recaptcha_status', '0'],
            ['recaptcha_site_key', ''],
            ['recaptcha_secret_key', ''],
            ['adsense_status', '0'],
            ['adsense_client_code', ''],
            ['analytics_status', '0'],
            ['analytics_code', ''],
            ['sharethis_status', '0'],
            ['sharethis_code', ''],
        ];

        foreach ($defaultSettings as $setting) {
            $connection->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)");
            $connection->bind(':key', $setting[0]);
            $connection->bind(':value', $setting[1]);
            $connection->execute();
        }

        // Insert default pages
        $connection->prepare("INSERT INTO pages (slug, deletable) VALUES (:slug, :deletable)");
        $connection->bind(':slug', 'about');
        $connection->bind(':deletable', 0);
        $connection->execute();

        $connection->prepare("INSERT INTO pages (slug, deletable) VALUES (:slug, :deletable)");
        $connection->bind(':slug', 'terms');
        $connection->bind(':deletable', 0);
        $connection->execute();

        $connection->prepare("INSERT INTO pages (slug, deletable) VALUES (:slug, :deletable)");
        $connection->bind(':slug', 'privacy');
        $connection->bind(':deletable', 0);
        $connection->execute();

        // Insert default roles
        $connection->prepare("INSERT INTO roles (title, size_limit) VALUES (:title, :size_limit)");
        $connection->bind(':title', 'User');
        $connection->bind(':size_limit', '150 MB');
        $connection->execute();

        $connection->prepare("INSERT INTO roles (title, size_limit) VALUES (:title, :size_limit)");
        $connection->bind(':title', 'Guest');
        $connection->bind(':size_limit', '50 MB');
        $connection->execute();

        $connection->prepare("INSERT INTO roles (title, size_limit) VALUES (:title, :size_limit)");
        $connection->bind(':title', 'Admin');
        $connection->bind(':size_limit', '500 MB');
        $connection->execute();

        // Insert languages
        foreach ($utils->getLanguages() as $code => $name) {
            $connection->prepare("INSERT INTO languages (language, language_code, is_active) VALUES (:language, :language_code, :is_active)");
            $connection->bind(':language', $name);
            $connection->bind(':language_code', $code);
            $connection->bind(':is_active', $code == 'en' ? 1 : 0);
            $connection->execute();
        }

        // Enable Production Mode
        $env_file = APP_PATH . "config/environment.php";
        $env_file_content = file_get_contents($env_file);
        $env_file_content = preg_replace("/installation/", "production", $env_file_content, 1);
        file_put_contents($env_file, $env_file_content);

        $msg = true;
    } catch (\PDOException $ex) {
        $error = $ex->getMessage();
        error_log($ex->getMessage() . "\n", 3, LOGS_PATH);
    } catch (\Exception $ex) {
        $error = $ex->getMessage();
        error_log($ex->getMessage() . "\n", 3, LOGS_PATH);
    }
}

$page = "installPage";
