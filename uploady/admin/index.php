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

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-folder mr-1"></i>
                            Latest Files
                        </div>
                        <div class="card-body">

                            <div class="table-responsive border pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                                <table class="table nowrap table-bordered" width="100%" id="filesTable" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Uploaded by</th>
                                            <th>Uploaded at</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($files_info as $file) : ?>
                                            <tr>
                                                <td><?= $file['filename']; ?></td>
                                                <td>
                                                    <?= $user->getByUserId($file['user_id']); ?>
                                                </td>
                                                <td><?= $file['uploaddate']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user mr-1"></i>
                            Latest Users
                        </div>
                        <div class="card-body">
                            <div class="table-responsive border pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                                <table class="table nowrap table-bordered" width="100%" id="usersTable" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email Address</th>
                                            <th>Active</th>
                                            <th>Settings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $u) : ?>
                                            <tr>
                                                <td><?= $u->username; ?></td>
                                                <td><?= $u->email; ?></td>
                                                <td><?= $u->is_active ? 'yes' : 'no'; ?></td>
                                                <td>
                                                    <a type="button" class="btn btn-primary" href="<?= $utils->siteUrl('/admin/users/edit.php?username=' . $u->username); ?>">
                                                        Edit User
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>

                </div>
            </main>
            <?php include_once 'components/footer.php'; ?>
        </div>
    </div>
    <?php include_once 'components/js.php'; ?>
    <?php $utils->script(
        'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js'
    ); ?>
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