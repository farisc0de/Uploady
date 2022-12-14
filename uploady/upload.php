<?php
include_once 'session.php';
include_once APP_PATH . "logic/uploadLogic.php";
?>

<?php include_once 'components/header.php' ?>
<title><?= $st['website_name'] ?> - <?= $lang['your_uploaded_files']; ?></title>
<?php include_once 'components/css.php' ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="row justify-content-center text-center">
          <div class="col-sm-12 col-md-9 col-lg-9">
            <div class="card">
              <div class="card-header">
                <?= $lang['your_uploaded_files']; ?>
              </div>
              <div class="card-body">
                <h4 class="card-title"><?= $lang['your_uploaded_files']; ?></h4>
                <hr />
                <div class="container">
                  <?php foreach ($resp as $msg) : ?>
                    <?php if (($msg['message'] != 5) && ($msg['message'] != 0)) : ?>
                      <?=
                      $utils->alert(
                        $msg['filename'] . ': ' . $upload->getMessage($msg['message']),
                        'danger',
                        'times-circle'
                      ); ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                  <?php foreach ($files as $file) : ?>
                    <div class="alert alert-success mb-1 mt-3">
                      <b><?= $file['filename'] . " : " . $upload->getMessage(0) ?></b>
                    </div>
                    <?= $file['filename']; ?>
                    <br />
                    <a class="btn btn-primary" href="<?= $file['downloadlink'] ?>">
                      <?= $lang['download_cta_btn']; ?>
                    </a>
                    <a class="btn btn-danger" href="<?= $file['deletelink'] ?>">
                      <?= $lang['delete_cta_btn']; ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include_once 'components/footer.php' ?>
  <?php include_once 'components/js.php' ?>
</body>

</html>