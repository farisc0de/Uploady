<?php include_once '../session.php'; ?>
<?php include_once 'logic/editLogic.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Edit Language - <?= $st['website_name'] ?></title>
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
                            Edit Language
                        </div>
                        <div class="card-body">
                            <form method="POST" action="actions/edit.php">

                                <input type="hidden" name="lang" value="<?= $_GET["lang"]; ?>">

                                <h3>General</h3>

                                <?php foreach ($language['general'] as $key => $value) : ?>
                                    <label><?= $key; ?></label>
                                    <input class="form-control" type="text" name="general_<?= $key ?>" value="<?= $value ?>">
                                <?php endforeach; ?>

                                <div class="m-3">
                                    <button class="btn btn-primary" name="update_general" type="submit">Update General</button>
                                </div>

                                <h3>Navbar</h3>

                                <?php foreach ($language['navbar'] as $key => $value) : ?>

                                    <label><?= $key; ?></label>
                                    <input class="form-control" type="text" name="navbar_<?= $key ?>" value="<?= $value ?>">

                                <?php endforeach; ?>

                                <div class="m-3">
                                    <button class="btn btn-primary" name="update_navbar" type="submit">Update Navbar</button>
                                </div>

                                <h3>Theme</h3>

                                <?php foreach ($language['theme'] as $key => $value) : ?>

                                    <label><?= $key; ?></label>
                                    <input class="form-control" type="text" name="theme_<?= $key ?>" value="<?= $value ?>">

                                <?php endforeach; ?>

                                <div class="m-3">
                                    <button class="btn btn-primary" name="update_theme" type="submit">Update Theme</button>
                                </div>
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