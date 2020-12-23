<?php include_once 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'components/meta.php'; ?>
    <title>Dashboard - <?= $st['website_name'] ?></title>
    <?php include_once 'components/css.php'; ?>
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            DataTable Example
                        </div>
                        <div class="card-body"></div>
                    </div>
                </div>
            </main>
            <?php include_once 'components/footer.php'; ?>
        </div>
    </div>
    <?php include_once 'components/js.php'; ?>
</body>

</html>