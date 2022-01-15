<?php
include_once '../session.php';
include_once 'logic/accountLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8" />
    <?php include_once '../components/header.php'; ?>
    <title>
        <?= $st['website_name'] ?> - Edit Account
    </title>
    <?php include_once '../components/css.php'; ?>
</head>

<body>

    <?php include_once '../components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <b>Edit Account</b>
                            </div>
                            <form method="POST" action="actions/update.php">
                                <div class="card-body">
                                    <?php if (isset($_GET['msg'])) : ?>

                                        <?php if ($_GET['msg'] == "yes") : ?>

                                            <?php echo $utils->alert(
                                                "User settings has been updated",
                                                "success",
                                                "check-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "csrf") : ?>

                                            <?php echo $utils->alert(
                                                "CSRF token is invalid.",
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "error") : ?>

                                            <?php echo $utils->alert(
                                                "An unexpected error has occurred",
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php elseif ($_GET['msg'] == "attack") : ?>

                                            <?php echo $utils->alert(
                                                "You are trying to access another account",
                                                "danger",
                                                "times-circle"
                                            ); ?>

                                        <?php endif; ?>

                                    <?php endif; ?>
                                    <?= $utils->input('id', $data->id); ?>

                                    <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="username" placeholder="Enter Username" value="<?= $data->username; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" name="email" placeholder="Enter Email" value="<?= $data->email; ?>">
                                    </div>
                                    <div class="mb-3" class="text-left">
                                        <input type="password" class="form-control" name="password" placeholder="Enter Password">
                                        <small>Keep it empty if you don't want to change the password.</small>
                                    </div>
                                    <a href="#" onclick="deleteAccount()">Delete Account</a>
                                </div>
                                <div class="card-footer mb-0">
                                    <button class="btn btn-primary">
                                        Edit Account
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

</html>