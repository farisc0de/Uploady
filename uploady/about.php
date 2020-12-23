<?php
include_once 'session.php';
include_once APP_PATH . 'logic/aboutLogic.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <?php include_once 'components/header.php'; ?>
  <title><?= $st['website_name'] ?> - About Us</title>
  <?php include_once 'components/css.php'; ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="card">
          <div class="card-header">
            About
            <?= $st['website_name'] ?>
          </div>
          <div class="card-body">
            <h3 class="card-title">About Us</h3>
            <p>
              <?= $st['website_name'] ?>
              is a free and anonymous file-sharing platform. You can store and
              share data of all types (files, images, music, videos etc...). There
              is no limit, you download at the maximum speed of your connection
              and everything is free.
            </p>

            <h3>Supported Formats</h3>
            <p>
              We support more then 50 format
              <a href="supported.php">See Here...</a>
            </p>

            <h3>No registration required</h3>
            <p>
              Use
              <?= $st['website_name'] ?>
              immediatly, no registration required.
            </p>

            <h3>Privacy and Anonymity</h3>
            <p>
              Respecting our users is essential. We do not store any personal
              data, we do not sell anything.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>
</body>

</html>