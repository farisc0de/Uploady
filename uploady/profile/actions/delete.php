<?php
include_once '../../session.php';

$handler = new \Uploady\Handler\UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        foreach ($_POST['fileid'] as $id) {
            if ($handler->fileExist($id) && $handler->userExist($_SESSION['user_id'])) {
                $file = json_decode($handler->getFile($id)->file_data);
                if ($handler->deleteFile($id, $_SESSION['user_id'])) {
                    unlink(
                        realpath(APP_PATH . UPLOAD_FOLDER . "/{$_SESSION['user_id']}/{$file->filename}")
                    );
                }
            }
        }

        $utils->redirect($utils->siteUrl('/profile/my_files.php?msg=file_deleted'));
    } else {
        $utils->redirect($utils->siteUrl('/profile/my_files.php?msg=csrf'));
    }
}
