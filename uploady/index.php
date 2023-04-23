<?php
include_once 'session.php';
include_once APP_PATH . 'logic/indexLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
  <div class="row justify-content-center text-center">
    <div class="col-sm-12 col-md-10 col-lg-10">
      <div class="card">
        <div class="card-header">
          <b><?= $lang['upload_form_card_title']; ?></b>
        </div>
        <div class="card-body">
          <form enctype="multipart/form-data" class="dropzone" id="my-dropzone" method="POST" action="actions/upload_file.php">
          </form>
        </div>

        <div class="card-footer mb-0">
          <p class="mb-0">
            <b><?= $lang['note_supported_formats']; ?></b>
            <a href="supported.php"><?= $lang['see_here']; ?></a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="text-center container">
  <ul class="list-inline">
    <li class="list-inline-item">
      <a href="#">
        <i class="fab fa-twitter fa-stack circle-twitter rounded-circle"></i>
      </a>
    </li>
    <li class="list-inline-item">
      <a href="#">
        <i class="fab fa-instagram fa-stack circle-instagram rounded-circle"></i>
      </a>
    </li>
    <li class="list-inline-item">
      <a href="#">
        <i class="fab fa-linkedin-in fa-stack circle-linkedin rounded-circle"></i>
      </a>
    </li>
  </ul>
</div>

<?php include_once 'components/footer.php'; ?>