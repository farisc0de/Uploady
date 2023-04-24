<?php
require_once  '../session.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $status = null;
    $settings = new Uploady\Settings($db);
    $upload = new \Farisc0de\PhpFileUploading\Upload();

    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf']) == false) {
        $status = "csrf";
    } else {

        if (isset($_FILES['website_logo'])) {
            $upload->setController(APP_PATH . "/vendor/farisc0de/phpfileuploading/src/");
            $upload->setSiteUrl(SITE_URL);

            $upload->setUploadFolder([
                "folder_name" => UPLOAD_FOLDER . "/settings",
                "folder_path" => realpath(APP_PATH . "/" . UPLOAD_FOLDER . "/settings"),
            ]);

            $upload->enableProtection();

            $upload->setSizeLimit(1000000);

            $upload->setUpload(new Farisc0de\PhpFileUploading\File($_FILES['website_logo']));

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

        unset($_FILES['website_logo']);
        $settings->updateSettings($utils->esc($_POST));

        $status = "yes";
    }

    $utils->redirect("view.php?msg=" . $utils->sanitize($status));
}
