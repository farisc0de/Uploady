<?php
include_once 'session.php';
include_once APP_PATH . 'logic/pageLogic.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <?php include_once 'components/header.php'; ?>
  <title> <?= $st['website_name'] ?> - <?= $page_content->title; ?></title>
  <?php include_once 'components/css.php'; ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="card">
          <div class="card-header">
            <?= $page_content->title; ?>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= $page_content->title; ?></h3>
            <?= $page_content->content; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>
</body>

</html>