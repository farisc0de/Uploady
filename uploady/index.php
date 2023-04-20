<?php
include_once 'session.php';
include_once APP_PATH . 'logic/indexLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
  <div class="row justify-content-center text-center">
    <div class="col-sm-12 col-md-8 col-lg-8">
      <div class="card">
        <div class="card-header">
          <b><?= $lang['upload_form_card_title']; ?></b>
        </div>
        <div class="card-body">
          <div id="upload_alert"></div>
          <div id="upload_alert">
            <?php if (isset($_GET['error'])) : ?>
              <?= $utils->alert(
                $lang['upload_bypass_message'],
                'danger'
              ); ?>
            <?php endif; ?>
          </div>
          <p class="card-title lead font-weight-bold text-dark">
            <?= $lang['upload_select_title']; ?>
          </p>

          <form enctype="multipart/form-data" role="form" method="POST" action="upload.php">
            <div id="dvFile">
              <div>
                <input type="file" class="form-control" name="file[]" />
              </div>
              <div class="pt-2">
                <input type="file" class="form-control" name="file[]" />
              </div>
              <div class="pt-2">
                <input type="file" class="form-control" name="file[]" />
              </div>
              <div class="pt-2">
                <input type="file" class="form-control" name="file[]" />
              </div>
            </div>

            <div class="text-start">
              <div class="form-check m-3">
                <input type="checkbox" class="form-check-input" id="tos">
                <label class="form-check-label" for="tos">
                  <?= $lang['i_agree'] ?> <a href="page.php?s=terms"><?= $lang['tos'] ?></a>
                </label>
              </div>
            </div>

            <button id="submit" name="submit" type="submit" class="btn btn-primary" disabled>
              <span class="fa fa-upload"></span> <?= $lang['upload_button_text'] ?>
            </button>
            <button type="button" id="add_more" class="btn btn-primary text-white">
              <span class="fa fa-plus"></span> <?= $lang['upload_add_more'] ?>
            </button>
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

<?php include_once 'components/footer.php'; ?>