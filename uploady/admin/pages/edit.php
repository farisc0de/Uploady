<?php include_once '../session.php'; ?>
<?php include_once 'logic/editLogic.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Dashboard - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../components/navbar.php' ?>
    <div id="layoutSidenav">
        <?php include_once '../components/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Add page
                        </div>
                        <div class="card-body">
                            <form method="POST" action="actions/update.php">

                                <input type="hidden" name="pageid" value="<?= $_GET['pageid']; ?>">

                                <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                                <?php if (isset($msg)) : ?>

                                    <?php if ($msg == "language_updated") : ?>

                                        <?php echo $utils->alert(
                                            "Page has been updated",
                                            "success",
                                            "check-circle"
                                        ); ?>

                                    <?php elseif ($msg == "csrf") : ?>

                                        <?php echo $utils->alert(
                                            "CSRF token is invalid.",
                                            "danger",
                                            "times-circle"
                                        ); ?>

                                    <?php elseif ($msg == "error") : ?>

                                        <?php echo $utils->alert(
                                            "An unexpected error has occurred",
                                            "danger",
                                            "times-circle"
                                        ); ?>

                                    <?php endif; ?>

                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="slug">Page Slug</label>
                                    <input type="text" class="form-control" name="slug" id="slug" aria-describedby="slug" placeholder="Enter page slug" value="<?= $slug ?>">
                                </div>

                                <button class="btn btn-primary" type="submit">Edit Page</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
</body>

</html>