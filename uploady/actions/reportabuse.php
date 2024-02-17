<?php

include_once '../session.php';

use Uploady\Mailer;

$tpl = new Uploady\Template(APP_PATH . 'template/emails/');

$handler = new Uploady\Handler\UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$handler->fileExist($_POST['fileid'])) {
        $utils->redirect(SITE_URL . "/reportabuse.php?msg=file_not_found&file_id={$utils->sanitize($_POST['fileid'])}");
    }

    $mailer = new Mailer($db);

    $mailer->sendMessage(
        $st["owner_email"],
        "Action Required: Report Abuse - " . $utils->sanitize($_POST['fileid']),
        $tpl->loadTemplate(
            'report_abuse',
            [
                'reporter' => $_POST['emailaddress'],
                'reported_url' => $_POST['fileid'],
                'reason' => $_POST['fileabusenote']
            ]
        )
    );

    $utils->redirect(SITE_URL . "/reportabuse.php?msg=report_sent&file_id={$_POST['fileid']}");
}
