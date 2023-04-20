<?php
include_once '../session.php';
include_once 'logic/accountLogic.php';
?>
<?php include_once '../components/header.php'; ?>
<title>
    <?= $st['website_name'] ?> - <?= $lang['edit_account_title']; ?>
</title>
<?php include_once '../components/css.php'; ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include_once '../components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <b> <?= $lang['edit_account_title']; ?></b>
                            </div>
                            <form method="POST" action="actions/update.php">
                                <div class="card-body">
                                    <?php if (isset($_GET['msg'])) : ?>

                                        <?php if ($_GET['msg'] == "yes") : ?>

                                            <?php echo $utils->alert(
                                                $lang['edit_account_success'],
                                                "success",
                                                "check-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "csrf") : ?>

                                            <?php echo $utils->alert(
                                                $lang['csrf_error'],
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "error") : ?>

                                            <?php echo $utils->alert(
                                                $lang['edit_account_error'],
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "attack") : ?>

                                            <?php echo $utils->alert(
                                                $lang['attack_error'],
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php endif; ?>

                                    <?php endif; ?>
                                    <?= $utils->input('id', $data->id); ?>

                                    <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="username" placeholder="<?= $lang['enter_username'] ?>" value="<?= $data->username; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" name="email" placeholder="<?= $lang['enter_your_email'] ?>" value="<?= $data->email; ?>">
                                    </div>
                                    <div class="mb-3" class="text-left">
                                        <input type="password" class="form-control" name="password" placeholder="<?= $lang['enter_password']; ?>">
                                        <small><?= $lang['keep_empty_msg']; ?></small>
                                    </div>
                                    <a href="auth.php"><?= $lang['enable_two_factor'] ?></a>
                                    |
                                    <a href="#" onclick="deleteAccount()"><?= $lang['delete_account']; ?></a>
                                </div>
                                <div class="card-footer mb-0">
                                    <button class="btn btn-primary">
                                        <?= $lang['edit_account_btn']; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../components/footer.php'; ?>

    <?php include_once '../components/js.php'; ?>

    <script>
        function deleteAccount() {
            var conf = confirm("Are you sure ?");
            if (conf == true) {
                window.location.href = 'actions/delete_me.php?token=<?php echo $_SESSION['csrf']; ?>';
            }
        }
    </script>
</body>

</html>