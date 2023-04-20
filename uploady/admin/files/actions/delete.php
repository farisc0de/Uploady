<?php
include_once '../../session.php';

$handler = new Uploady\Handler\UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['fileid'] as $id) {;
        $file = json_decode($handler->getFile($id)->file_data);
        $handler->deleteFileAsAdmin($id);
        if (file_exists("uploads/{$_SESSION['user_id']}/{$file->filename}")) {
            unlink(
                realpath(APP_PATH . "uploads/{$_SESSION['user_id']}/{$file->filename}")
            );
        }
    }

    $utils->redirect($utils->siteUrl('/admin/files/view.php?msg=ok'));
}
