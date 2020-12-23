<?php
include_once 'session.php';
include_once 'logic/homeLogic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'components/meta.php'; ?>
    <title>Dashboard - <?= $st['website_name'] ?></title>
    <?php include_once 'components/css.php'; ?>

    <?php $utils->style(
        'https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'
    ); ?>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'components/navbar.php' ?>
    <div id="layoutSidenav">
        <?php include_once 'components/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <?php include_once 'components/charts.php'; ?>
                </div>
            </main>
            <?php include_once 'components/footer.php'; ?>
        </div>
    </div>
    <?php include_once 'components/js.php'; ?>
    <?php $utils->script(
        'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js'
    ); ?>
    <?php $utils->script('chart-area-demo.js', 'admin/assets/demo'); ?>
    <?php $utils->script('chart-bar-demo.js', 'admin/assets/demo'); ?>
    <?php $utils->script(
        'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"'
    ); ?>
    <?php $utils->script(
        'https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'
    ); ?>
    <?php $utils->script('datatables-demo.js', 'admin/assets/demo'); ?>
</body>

</html>