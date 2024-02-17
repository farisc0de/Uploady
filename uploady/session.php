<?php

session_start();

include_once 'config/config.php';

if (isset($_SESSION)) {

    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $data = null;

    if (!isset($_SESSION['user_role'])) {
        $_SESSION['user_role'] = 2;
    }

    if ($username != null) {
        if (isset($_SESSION['loggedin'])) {
            $data = $user->get($username);
        }

        if (!isset($_SESSION['current_ip'])) {
            $_SESSION['current_ip'] = $utils->sanitize($_SERVER['REMOTE_ADDR']);
        }

        if (!(isset($_SESSION['csrf']))) {
            $auth->generateSessionToken();
        }


        $_SESSION['user_id'] = $data->user_id;

        // Two Factor Authentication

        if ($user->isTwoFAEnabled($username) == true) {
            if (!isset($_SESSION['OTP']) || $_SESSION['OTP'] != true) {
                if (!strpos($current_url, "auth.php")) {
                    $utils->redirect($utils->siteUrl("/auth.php"));
                }
            }
        }

        if (isset($_SESSION['isHuman'])) {
            if ($_SESSION['isHuman'] == false) {
                $utils->redirect($utils->siteUrl('/logout.php'));
            }
        }
    }


    if (!isset($_SESSION['user_id'])) {
        $_SESSION["user_id"] = hash("sha256", "user-" . session_id());
    }

    // Public Uploads handling

    if (!isset($_SESSION['loggedin'])) {
        if (!in_array(basename($_SERVER['PHP_SELF']), $allowed_pages)) {
            if (!$settings->getSettingValue("public_upload")) {
                $utils->redirect($utils->siteUrl('/login.php'));
            }
        }
    }

    if (strpos($current_url, "profile/")) {
        if (!isset($_SESSION['loggedin'])) {
            $utils->redirect($utils->siteUrl('/login.php'));
        }
    }

    // Refresh session id every 5 minutes

    if (!isset($_SESSION['last_token_refresh'])) {
        $_SESSION['last_token_refresh'] = time();
        session_regenerate_id(true);
    } else {
        if (time() - $_SESSION['last_token_refresh'] > 300) {
            $_SESSION['last_token_refresh'] = time();
            session_regenerate_id(true);
        }
    }
}

$language = $_GET['lang'] ?? $localization->getLanguage();
$direction = $localization->getLanguageByCode($language)->language_direction;
$theme = $_GET['theme'] ?? $_SESSION['theme'] ?? 'light';

$dir = "dir=\"{$direction}\" lang=\"{$language}\"";

if ($theme == 'dark') {
    $_SESSION['theme'] = 'dark';
    $theme = 'dark';
} else {
    $_SESSION['theme'] = 'light';
    $theme = 'light';
}

$localization->setLanguage($language);
$lang = $localization->loadLangauge($localization->getLanguage());

$page = 'session';
