<?php
require_once  '../../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $status = null;
    $settings = new Uploady\Settings($db);
    $upload = new \Farisc0de\PhpFileUploading\Upload(new \Farisc0de\PhpFileUploading\Utility());

    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $status = "csrf";
    } else {

        if (isset($_POST['delete_logo'])) {
            $settings->updateSettings(
                [
                    "website_logo" => ""
                ]
            );
            $status = "settings_updated";
            $utils->redirect("view.php?msg=" . $utils->sanitize($status));
        }

        if (isset($_POST['delete_favicon'])) {
            $settings->updateSettings(
                [
                    "website_favicon" => ""
                ]
            );
            $status = "settings_updated";
            $utils->redirect("view.php?msg=" . $utils->sanitize($status));
        }

        if (isset($_FILES['website_logo'])) {
            $upload->setSiteUrl(SITE_URL);

            $upload->setUploadFolder([
                "folder_name" => UPLOAD_FOLDER . "/settings",
                "folder_path" => realpath(APP_PATH . "/" . UPLOAD_FOLDER . "/settings"),
            ]);

            $upload->enableProtection();

            $upload->setSizeLimit(1000000);

            $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['website_logo'], new \Farisc0de\PhpFileUploading\Utility()));

            if ($upload->checkIfNotEmpty()) {

                $upload->hashName();

                if ($upload->checkSize()) {
                    if (
                        $upload->checkForbidden() &&
                        $upload->checkExtension() &&
                        $upload->checkMime()
                    ) {
                        if ($upload->upload()) {
                            $settings->updateSettings([
                                "website_logo" => $upload->generateDirectDownloadLink()
                            ]);
                        }
                    }
                }
            }
        }

        if (isset($_FILES['website_favicon'])) {
            $upload->setSiteUrl(SITE_URL);

            $upload->setUploadFolder([
                "folder_name" => UPLOAD_FOLDER . "/settings",
                "folder_path" => realpath(APP_PATH . "/" . UPLOAD_FOLDER . "/settings"),
            ]);

            $upload->enableProtection();

            $upload->setSizeLimit(1000000);

            $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['website_favicon'], new \Farisc0de\PhpFileUploading\Utility()));

            if ($upload->checkIfNotEmpty()) {

                $upload->hashName();

                if ($upload->checkSize()) {
                    if (
                        $upload->checkForbidden() &&
                        $upload->checkExtension() &&
                        $upload->checkMime()
                    ) {
                        if ($upload->upload()) {
                            $settings->updateSettings([
                                "website_favicon" => $upload->generateDirectDownloadLink()
                            ]);
                        }
                    }
                }
            }
        }

        unset($_FILES['website_logo']);
        $settings->updateSettings($utils->esc($_POST));

        $status = "settings_updated";
    }

    $utils->redirect($utils->siteUrl('/admin/settings/view.php?msg=' . $utils->sanitize($status)));
}
