<?php
$upload = new Uploady\Handler\Upload();

$mailer = new Uploady\Mailer($db);

$tpl = new Uploady\Template('template/emails');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $utils->sanitize($_POST['username']);
    $email = $utils->sanitize($_POST['email']);
    $password = $utils->sanitize($_POST['password']);

    if (!$user->checkUser($email) && !$user->checkUser($username)) {
        $token = bin2hex(random_bytes(16));

        $hash = sha1($token);

        $user->createUser([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'user_id' => $upload->generateUserID(),
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

        $msg = "Account created";
    } else {
        $error = "User already exist";
    }
}

$page = "signupPage";
