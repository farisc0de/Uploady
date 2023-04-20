<?php
include_once 'session.php';
include_once APP_PATH . 'logic/pageLogic.php';
?>

<?php include_once 'components/header.php'; ?>

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

<?php include_once 'components/footer.php'; ?>