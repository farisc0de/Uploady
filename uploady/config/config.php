<?php

// Database Settings
define("DB_HOST", "db");
define("DB_USER", "uploady");
define("DB_PASS", "uploady");
define("DB_NAME", "uploady");

// Application Settings
define("SITE_URL", "http://localhost");
define("APP_PATH", dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
define("LOGS_PATH", APP_PATH . "php_logs.log");

// Upload Settings
define("MAX_SIZE", "1 GB");
define("UPLOAD_FOLDER", "uploads");

// Environment Settings
require_once 'environment.php';

// Autoload Composer
require_once APP_PATH . 'vendor/autoload.php';

// Initialize Application
require_once APP_PATH . 'init.php';
