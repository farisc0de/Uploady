<?php
include_once '../session.php';
include_once 'logic/viewLogic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>View Languages - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
    <?php $utils->style(
        'https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'
    ); ?>
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

                    <?php if (isset($msg)) : ?>
                        <?php $utils->loadAlerts($msg, "language");  ?>
                    <?php endif; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-globe mr-1"></i>
                            Manage Languages
                        </div>
                        <div class="card-body">
                            <div class="table-responsive border pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                                <table class="table nowrap table-bordered" width="100%" id="dataTable" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Language Name</th>
                                            <th>Language Code</th>
                                            <th>Settings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($languages as $language) : ?>
                                            <tr>
                                                <td><?= $language->language; ?>
                                                </td>
                                                <td><?= $language->language_code ?></td>
                                                <td>
                                                    <?php if (!$language->is_active) : ?>
                                                        <a type="button" class="btn btn-primary" href="<?= $utils->siteUrl('/admin/languages/actions/changeStatus.php?lang=' . $language->language_code . "&status=active"); ?>">
                                                            Enable Language
                                                        </a>
                                                    <?php else : ?>
                                                        <a type="button" class="btn btn-primary" href="<?= $utils->siteUrl('/admin/languages/edit.php?lang=' . $language->language_code); ?>">
                                                            Edit Language
                                                        </a>
                                                        <a type="button" class="btn btn-danger" href="<?= $utils->siteUrl('/admin/languages/actions/changeStatus.php?lang=' . $language->language_code . "&status=deactive"); ?>">
                                                            Disable Language
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
    <?php $utils->script(
        'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'
    ); ?>
    <?php $utils->script(
        'https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'
    ); ?>
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $("#dataTable").DataTable({
                ordering: true,
            });
        });
    </script>
</body>

</html>