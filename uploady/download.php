<?php
include_once 'session.php';
include_once APP_PATH . 'logic/downloadLogic.php';
?>

<?php include_once 'components/header.php'; ?>
<title><?= $st['website_name'] ?> - <?= $lang['download_file_title'] ?></title>
<?php include_once 'components/css.php'; ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pt-5 pb-5">
        <div class="row justify-content-center text-center">
          <div class="col-9">
            <div class="card">
              <div class="card-header">
                <?= $lang['download_file_title'] ?>
              </div>

              <div class="card-body">
                <h4 class="card-title"></h4>
                <div class="container">
                  <div class="ml-auto">
                    <div class="alert">
                      <div class="row border border-primary rounded">
                        <div class="col-sm-12 col-md-12 col-lg-4 text-center" id="qr_code">
                          <img class="mr-auto justify-content-center" src="<?= $file_data->qrcode ?>" title="QR Code" />
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-8 pt-2 text-start bg-light" id="upload_info">
                          <div class="col-12 text-dark mb-3 mr-3 ml-3 text-underline">
                            <p>
                              <u><?= $lang['file_name']; ?>:
                                <?= $file_data->filename ?></u>
                            </p>
                          </div>

                          <div class="col-sm-auto text-dark mt-3 mb-3 mr-3 ml-3">
                            <p>
                              <u><?= $lang['file_hash']; ?>:
                                <?= $file_data->filehash ?></u>
                            </p>
                          </div>

                          <div class="col-12 text-dark mt-3 mb-3 mr-3 ml-3">
                            <p>
                              <u><?= $lang['file_size']; ?>:
                                <?= $file_data->filesize ?></u>
                            </p>
                          </div>

                          <div class="col-12 text-dark mt-3 mr-3 ml-3 mb-4">
                            <p>
                              <u><?= $lang['upload_date']; ?>:
                                <?= $file_data->uploaddate ?></u>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="pt-3">
                        <a href="<?= $file_data->directlink ?>" class="btn btn-primary text-decoration-none" download>
                          <?= $lang['download_button']; ?>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>
</body>

</html>