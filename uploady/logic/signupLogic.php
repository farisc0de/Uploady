<?php
$upload = new \Farisc0de\PhpFileUploading\Upload(new \Farisc0de\PhpFileUploading\Utility());

$mailer = new Uploady\Mailer($db);

$tpl = new Uploady\Template('template/emails');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $utils->sanitize($_POST['username']);
    $email = $utils->sanitize($_POST['email']);
    $password = $utils->sanitize($_POST['password']);

    if (!$user->isExist($email) && !$user->isExist($username)) {
        $token = bin2hex(random_bytes(16));

        $hash = sha1($token);

        $upload->generateUserID();

        $user->add([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'user_id' => $upload->getUserID(),
            'activation_hash' => $hash,
            'is_active' => 0
        ]);

        $url = $utils->siteUrl("/activate.php?token=$token");

        $mailer->sendMessage(
            $email,
            'Account Activation',
            $tpl->loadTemplate('activation_email', [
                'username' => $username,
                'activation_url' => $url
            ])
        );

        $msg = $lang['signup_success'];
    } else {
        $error = $lang['user_already_exist'];
    }
}

$page = "signupPage";
$title = $lang['signup_title'];
