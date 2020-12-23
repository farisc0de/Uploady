<?php
include_once '../../session.php';

$upload = new \Uploady\Handler\UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->checkToken($_POST['csrf'], $_SESSION['csrf'])) {
        foreach ($_POST['fileid'] as $id) {
            if ($upload->fileExist($id)) {
                $upload->deleteFile($id, $_SESSION['user_id']);
            }
        }

        $utils->redirect($utils->siteUrl('/profile/my_files.php?msg=ok'));
    } else {
        $utils->redirect($utils->siteUrl('/profile/my_files.php?msg=csrf'));
    }
}
