<?php
include_once '../session.php';
include_once '../logic/settingsLogic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Dashboard - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
    <?php $utils->style('css/tagsinput.css', 'admin/assets') ?>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../components/navbar.php' ?>
    <div id="layoutSidenav">
        <?php include_once '../components/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-wrench mr-1"></i>
                            Edit Settings
                        </div>
                        <div class="card-body">
                            <form method="POST" action="edit.php">

                                <div class="container container-special">
                                    <?php if (isset($_GET['msg']) && $_GET['msg'] == "yes") : ?>
                                        <?= $utils->alert("Settings has been updated", "success", "check-circle"); ?>
                                    <?php endif; ?>

                                    <?php if (isset($_GET['msg']) && $_GET['msg'] == "csrf") : ?>
                                        <?= $utils->alert("CSRF token is invalid.", "danger", "times-circle"); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="container container-special">
                                    <div class="align-content-center justify-content-center">
                                        <?= $utils->input("csrf", $utils->sanitize($_SESSION['csrf'])); ?>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="website_name" type="text" name="website_name" placeholder="Website Name" value="<?= $settings->getSettingValue('website_name'); ?>">
                                                <label for="website_name">Website Name</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="description" type="text" name="description" placeholder="Website Name" value="<?= $settings->getSettingValue('description'); ?>">
                                                <label for="website_name">Website Description</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input class="form-control" id="keywords" type="text" name="keywords" placeholder="Enter Keyword" data-role="tagsinput" value="<?= $settings->getSettingValue('keywords'); ?>">
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="owner_name" type="text" name="owner_name" placeholder="Owner Name" value="<?= $settings->getSettingValue('owner_name'); ?>">
                                                <label for="website_name">Owner Name</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="email" type="text" name="email" placeholder="Owner Email" value="<?= $settings->getSettingValue('owner_email'); ?>">
                                                <label for="email">Owner Email</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <select label="Select a Theme" name="theme_name" id="theme_name" class="form-control custom-select">
                                                <option>Select a Theme</option>
                                                <?php foreach ($bootswatch_themes as $key => $value) : ?>
                                                    <option value="<?= $key; ?>" <?= ($settings->getSettingValue('theme_name') == $key) ? "selected" : null; ?>><?= $value ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <hr />

                                        <div class="form-group">
                                            <input hidden name="recaptcha_status" value="0" />
                                            <div class="custom-control custom-switch custom-control-right">
                                                <input class="custom-control-input" id="recaptcha_status" name="recaptcha_status" value="1" type="checkbox" <?= ($settings->getSettingValue('recaptcha_status') == true) ? 'checked' : null; ?>>
                                                <label class="custom-control-label" for="recaptcha_status">Enable reCAPTCHA</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="recaptchapublic" type="text" name="recaptchapublic" placeholder="reCAPTCHA Public Key" value="<?= $settings->getSettingValue('recaptcha_site_key'); ?>">
                                                <label for="recaptchapublic">reCAPTCHA Public Key</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="recaptchaprivate" type="text" name="recaptchaprivate" placeholder="reCAPTCHA Public Key" value="<?= $settings->getSettingValue('recaptcha_secret_key'); ?>">
                                                <label for="recaptchaprivate">reCAPTCHA Private Key</label>
                                            </div>
                                            <small>
                                                Note: This feature requires a free site and secret key from <a href="https://www.google.com/recaptcha">Google reCaptcha</a>
                                            </small>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <input hidden name="smtp_status" value="0" />
                                            <div class="custom-control custom-switch custom-control-right">
                                                <input class="custom-control-input" id="smtp_status" value="1" name="smtp_status" type="checkbox" <?= ($settings->getSettingValue('smtp_status') == true) ? 'checked' : null; ?>>
                                                <label class="custom-control-label" for="smtp_status">Enable SMTP</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="smtp_host" name="smtp_host" placeholder="SMTP Host" value="<?= $settings->getSettingValue('smtp_host'); ?>">
                                                <label for="smtp_host">SMTP Host</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="smtp_username" name="smtp_username" placeholder="SMTP User" value="<?= $settings->getSettingValue('smtp_username'); ?>">
                                                <label for="smtp_username">SMTP User</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="password" id="smtp_password" name="smtp_password" placeholder="SMTP Password" value="<?= $settings->getSettingValue('smtp_password'); ?>">
                                                <label for="smtp_password">SMTP Password</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <select label="Select a Security type" name="smtp_security" id="smtp_security" class="form-control custom-select">
                                                <option>Select a Security type</option>
                                                <?php foreach ($smtp_types as $smtp_type) : ?>
                                                    <option value="<?= strtolower($smtp_type); ?>" <?= ($settings->getSettingValue('smtp_security') == strtolower($smtp_type)) ? "selected" : null; ?>><?= $smtp_type ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="smtp_port" name="smtp_port" placeholder="SMTP Port" value="<?= $settings->getSettingValue('smtp_port'); ?>">
                                                <label for="smtp_port">SMTP Port</label>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <input hidden name="adsense_status" value="0" />
                                            <div class="custom-control custom-switch custom-control-right">
                                                <input class="custom-control-input" id="adsense_status" name="adsense_status" value="1" type="checkbox" <?= ($settings->getSettingValue('adsense_status') == true) ? 'checked' : null; ?>>
                                                <label class="custom-control-label" for="adsense_status">Enable Adsense</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="adsense_client_code" name="adsense_client_code" placeholder="Adsense Client Code" value="<?= $settings->getSettingValue('adsense_client_code'); ?>">
                                                <label for="adsense_client_code">Adsense Client Code</label>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary btn-block">
                                            Update Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
    <?php $utils->script('js/tagsinput.js', 'admin/assets') ?>
</body>

</html>