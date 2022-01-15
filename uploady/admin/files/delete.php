<?php
include_once '../session.php';

$handler = new Uploady\Handler\UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['fileid'] as $id) {
        $handler->deleteFileAsAdmin($id);
    }

    $utils->redirect($utils->siteUrl('/admin/files/view.php?msg=ok'));
}
