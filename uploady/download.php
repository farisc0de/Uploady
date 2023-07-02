<?php
include_once 'session.php';
include_once APP_PATH . 'logic/downloadLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pt-5 pb-5">
  <div class="row justify-content-center text-center">
    <div class="col-9">
      <div class="card">
        <div class="card-header">
          <?= $lang["general"]['download_file_title'] ?>
        </div>

        <div class="card-body">
          <h4 class="card-title"></h4>
          <div class="container">
            <div class="row border border-primary rounded bg-body-tertiary">
              <div class="col-sm-12 col-md-12 col-lg-4 text-center pt-sm-0 pt-md-3">
                <img class="mr-auto justify-content-center rounded" src="<?= $file_data->qrcode ?>" title="QR Code" />
              </div>
              <div class="col-sm-12 col-md-12 col-lg-8 pt-3 text-start d-none d-md-block">
                <ul class="list-unstyled">
                  <li class=" mb-3 mr-3 ml-3">
                    <b><?= $lang["general"]['file_name']; ?>:</b>
                    <?= $file_data->filename ?>
                  </li>
                  <li class="mt-3 mb-3 mr-3 ml-3">
                    <b><?= $lang["general"]['file_hash']; ?>:</b>
                    <?= $file_data->filehash ?>
                  </li>
                  <li class=" mb-3 mr-3 ml-3">
                    <b><?= $lang["general"]['file_size']; ?>:</b>
                    <?= $file_data->filesize ?>
                  </li>
                  <li class="mt-3 mr-3 ml-3 mb-4">
                    <b><?= $lang["general"]['upload_date']; ?>:</b>
                    <?= $file_data->uploaddate ?>
                  </li>
                </ul>
              </div>
            </div>

            <?php if ($settings->getSettingValue('sharethis_status')) : ?>

              <div class="pt-3 pb-2">
                <div class="sharethis-inline-share-buttons"></div>
              </div>

            <?php endif; ?>

            <div class="pt-3">
              <ul class="list-inline">
                <li class="list-inline-item">
                  <a href="<?= $file_data->directlink ?>" class="btn btn-primary text-decoration-none" download>
                    <?= $lang["general"]['download_button']; ?>
                  </a>
                </li>
                <li class="list-inline-item mt-1">
                  <a href="reportabuse.php?file_id=<?= $file_data->file_id ?>" class="btn btn-danger text-decoration-none">
                    <?= $lang["general"]['report_abuse']; ?>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'components/footer.php'; ?>