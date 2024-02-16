<?php
include_once '../session.php';
include_once 'logic/viewLogic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>View pages - <?= $st['website_name'] ?></title>
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

                        <?php $utils->loadAlerts($msg, "page");  ?>

                    <?php endif; ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-users mr-1"></i>
                            Manage Pages
                        </div>
                        <form method="POST" action="<?= $utils->siteUrl('/admin/pages/actions/delete.php') ?>">
                            <?= $utils->input('csrf', $_SESSION['csrf']); ?>
                            <div class="card-body">
                                <div class="table-responsive border pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                                    <table class="table nowrap table-bordered" width="100%" id="dataTable" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="select-all" name="select-all">
                                                        <label class="custom-control-label" for="select-all"></label>
                                                    </div>
                                                </th>
                                                <th>Page Slug</th>
                                                <th>Is Deletable</th>
                                                <th>Created at</th>
                                                <th>Settings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pages as $p) : ?>
                                                <tr>
                                                    <td>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="page_<?= $p->id ?>" name="slug[]" value="<?= $p->slug; ?>" />
                                                            <label class="custom-control-label" for="page_<?= $p->id; ?>" </label>
                                                        </div>
                                                    </td>
                                                    <td><?= $p->slug; ?>
                                                    </td>
                                                    <td><?= $p->deletable ? "true" : "false"; ?></td>
                                                    <td><?= $p->created_at ?></td>
                                                    <td>
                                                        <a type="button" class="btn btn-primary" href="<?= $utils->siteUrl('/admin/pages/edit.php?pageid=' . $p->id); ?>">
                                                            Edit Page
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    Delete Page
                                </button>
                                <a type="button" class="btn btn-primary" href="<?= $utils->siteUrl('/admin/pages/add.php'); ?>">
                                    Create Page
                                </a>
                            </div>
                        </form>
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

                select: {
                    style: "multi",
                },
                order: [
                    [1, null]
                ],
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                }, ],
            });
        });
        $("#select-all").click(function(event) {
            if (this.checked) {
                $(":checkbox").each(function() {
                    if ($(this).prop('disabled') == false) {
                        this.checked = true;
                    }
                });
            } else {
                $(":checkbox").each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
</body>

</html>