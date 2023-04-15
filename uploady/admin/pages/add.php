<?php
include_once '../session.php';
include_once 'logic/editLogic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Add a page - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../components/navbar.php' ?>

    <div id="layoutSidenav">
        <?php include_once '../components/sidebar.php'; ?>
        <div id="layoutSidenav_content">

            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>

    <?php include_once '../components/js.php'; ?>
</body>

</html>