<?php
include_once 'session.php';
include_once APP_PATH . "logic/uploadLogic.php";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <?php include_once 'components/header.php' ?>
  <title><?= $st['website_name'] ?> - Your Files</title>
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
                Your Uploaded Files
              </div>
              <div class="card-body">
                <h4 class="card-title">Your Files</h4>
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
                      Click Here to Download
                    </a>
                    <a class="btn btn-danger" href="<?= $file['deletelink'] ?>">
                      Click Here to Delete
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