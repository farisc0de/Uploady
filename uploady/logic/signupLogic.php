<?php
$upload = new \Farisc0de\PhpFileUploading\Upload(new \Farisc0de\PhpFileUploading\Utility());

$mailer = new Uploady\Mailer($db);

$tpl = new Uploady\Template('template/emails');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $utils->sanitize($_POST['username']);
    $email = $utils->sanitize($_POST['email']);
    $password = $utils->sanitize($_POST['password']);

    if (!$user->isExist($email) && !$user->isExist($username)) {
        if (isset($_POST['recaptcha_response'])) {
            $recaptcha = new \ReCaptcha\ReCaptcha($settings->getSettingValue('recaptcha_secret_key'));

            $resp = $recaptcha->setChallengeTimeout(60)
                ->setExpectedAction("login_form")
                ->setScoreThreshold(0.5)
                ->verify($_POST['recaptcha_response'], $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()) {
                $error = $lang["general"]["recaptcha_failed"];
            }
        }

        if (!isset($error)) {
            $token = bin2hex(random_bytes(16));

            $hash = hash("sha256", $token);

            $upload->generateUserID();

            $user->add([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'user_id' => $upload->getUserID(),
                "api_key" => bin2hex(random_bytes(16)),
                'activation_hash' => $hash,
                'is_active' => 0
            ]);

            $mailer->sendMessage(
                $email,
                $lang["general"]['activation_email_subject'],
                $tpl->loadTemplate('activation_email', [
                    'username' => $username,
                    'activation_url' => $utils->siteUrl("/activate.php?token=$token")
                ])
            );

            $msg = $lang["general"]['signup_success'];
        }
    } else {
        $error = $lang["general"]['user_already_exist'];
    }
}

$page = "signupPage";
$title = $lang["general"]['signup_title'];
