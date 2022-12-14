<?php
include_once 'session.php';
include_once APP_PATH . 'logic/deleteLogic.php';
?>

<?php include_once 'components/header.php'; ?>
<title><?= $st['website_name'] ?> - <?= $lang['delete_file_title'] ?></title>
<?php include_once 'components/css.php'; ?>
</head>

<body class="d-flex flex-column h-100">

  <?php include_once 'components/navbar.php'; ?>

  <main class="flex-shrink-0">
    <div class="container pt-5 pb-5">
      <div class="row justify-content-center text-center">
        <div class="col-9">
          <div class="card">
            <div class="card-header">
              <?= $lang['delete_file_title'] ?>
            </div>

            <div class="card-body">
              <h4 class="card-title"></h4>
              <div class="container">
                <div class="ml-auto">
                  <div class="alert">
                    <div class="border border-primary bg-primary rounded">
                      <p class="pt-3 text-light"><?= $msg; ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>
</body>

</html>