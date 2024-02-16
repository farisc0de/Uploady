<?php
include_once '../session.php';
include_once 'logic/settings.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Edit Settings - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
    <?php $utils->style('css/bootstrap-tagsinput.css', 'admin/assets'); ?>
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
                            <form method="POST" action="<?= $utils->siteUrl("/admin/settings/actions/edit.php"); ?>" enctype="multipart/form-data">
                                <div class="container container-special">

                                    <?php if (isset($msg)) : ?>

                                        <?php $utils->loadAlerts($msg, "settings");  ?>

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
                                                <input class="form-control" id="website_headline" type="text" name="website_headline" placeholder="Website Headline" value="<?= $settings->getSettingValue('website_headline'); ?>">
                                                <label for="website_headline">Website Headline</label>
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
                                            <?php if ($settings->getSettingValue("website_logo")) : ?>
                                                <img src="<?= $settings->getSettingValue("website_logo") ?>" height="64">
                                                <button type="submit" name="delete_logo" class="btn btn-danger">Delete</button>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="logo" name="website_logo" aria-describedby="logo">
                                                <label class="custom-file-label" for="logo">
                                                    Choose Website Logo
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <?php if ($settings->getSettingValue("website_favicon")) : ?>
                                                <img src="<?= $settings->getSettingValue("website_favicon") ?>" height="64">
                                                <button type="submit" name="delete_favicon" class="btn btn-danger">Delete</button>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="favicon" name="website_favicon" aria-describedby="favicon">
                                                <label class="custom-file-label" for="favicon">
                                                    Choose Website Icon
                                                </label>
                                            </div>
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

                                        <hr />

                                        <div class="form-group">
                                            <input hidden name="public_upload" value="0" />
                                            <div class="custom-control custom-switch custom-control-right">
                                                <input class="custom-control-input" id="public_upload" name="public_upload" value="1" type="checkbox" <?= ($settings->getSettingValue('public_upload') == true) ? 'checked' : null; ?>>
                                                <label class="custom-control-label" for="public_upload">Public Upload</label>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="instagram_link" name="instagram_link" placeholder="Instagram Link" value="<?= $settings->getSettingValue('instagram_link'); ?>">
                                                <label for="instagram_link"><i class="fab fa-instagram"></i> Instagram Link</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="twitter_link" name="twitter_link" placeholder="Twitter Link" value="<?= $settings->getSettingValue('twitter_link'); ?>">
                                                <label for="twitter_link"><i class="fab fa-twitter"></i> Twitter Link</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" type="text" id="linkedin_link" name="linkedin_link" placeholder="LinkedIn Link" value="<?= $settings->getSettingValue('linkedin_link'); ?>">
                                                <label for="linkedin_link"><i class="fab fa-linkedin-in"></i> LinkedIn Link</label>
                                            </div>
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
                                                <input class="form-control" id="recaptchapublic" type="text" name="recaptcha_site_key" placeholder="reCAPTCHA Public Key" value="<?= $settings->getSettingValue('recaptcha_site_key'); ?>">
                                                <label for="recaptchapublic">reCAPTCHA Public Key</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <input class="form-control" id="recaptchaprivate" type="text" name="recaptcha_secret_key" placeholder="reCAPTCHA Private Key" value="<?= $settings->getSettingValue('recaptcha_secret_key'); ?>">
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
                                            <input hidden name="maintenance_mode" value="0" />
                                            <div class="custom-control custom-switch custom-control-right">
                                                <input class="custom-control-input" id="maintenance_mode" name="maintenance_mode" value="1" type="checkbox" <?= ($settings->getSettingValue('maintenance_mode') == true) ? 'checked' : null; ?>>
                                                <label class="custom-control-label" for="maintenance_mode">Enable Maintenance Mode</label>
                                            </div>
                                        </div>

                                        <hr>

                                        <?php if ($utils->module_exist("adsense")) : ?>

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

                                        <?php endif; ?>


                                        <?php if ($utils->module_exist("analytics")) : ?>

                                            <div class="form-group">
                                                <input hidden name="analytics_status" value="0" />
                                                <div class="custom-control custom-switch custom-control-right">
                                                    <input class="custom-control-input" id="analytics_status" name="analytics_status" value="1" type="checkbox" <?= ($settings->getSettingValue('analytics_status') == true) ? 'checked' : null; ?>>
                                                    <label class="custom-control-label" for="analytics_status">Enable Google Analytics</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <input class="form-control" type="text" id="analytics_code" name="analytics_code" placeholder="Analytics UA Code" value="<?= $settings->getSettingValue('analytics_code'); ?>">
                                                    <label for="analytics_code">Google Analytics Code</label>
                                                </div>
                                            </div>

                                        <?php endif; ?>

                                        <?php if ($utils->module_exist("sharethis")) : ?>

                                            <div class="form-group">
                                                <input hidden name="sharethis_status" value="0" />
                                                <div class="custom-control custom-switch custom-control-right">
                                                    <input class="custom-control-input" id="sharethis_status" name="sharethis_status" value="1" type="checkbox" <?= ($settings->getSettingValue('sharethis_status') == true) ? 'checked' : null; ?>>
                                                    <label class="custom-control-label" for="sharethis_status">Enable ShareThis</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <input class="form-control" type="text" id="sharethis_code" name="sharethis_code" placeholder="Sharethis Code" value="<?= $settings->getSettingValue('sharethis_code'); ?>">
                                                    <label for="sharethis_code">Sharethis Code</label>
                                                </div>
                                            </div>

                                        <?php endif; ?>

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
    <?php $utils->script("https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"); ?>

    <script>
        $("#keywords").val()
        bsCustomFileInput.init()
    </script>

</body>

</html>