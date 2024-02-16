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
          <b><?= $lang["general"]['upload_form_card_title']; ?></b>
        </div>
        <div class="card-body">
          <input type="hidden" id="max_file_size" value="<?= $utility->unitConvert($utility->sizeInBytes(MAX_SIZE), "MB"); ?>">
          <form enctype="multipart/form-data" class="dropzone" id="my-dropzone" method="POST" action="actions/upload_file.php">
            <div class="fallback">
              <input name="file" type="file" multiple />
            </div>
          </form>
        </div>

        <div class="card-footer mb-0">
          <p class="mb-0">
            <b><?= $lang["general"]['note_supported_formats']; ?></b>
            <a href="supported.php"><?= $lang["general"]['see_here']; ?></a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'components/footer.php'; ?>