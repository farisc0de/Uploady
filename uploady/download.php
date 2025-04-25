<?php
include_once 'session.php';
include_once APP_PATH . 'logic/downloadLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header py-3">
          <h5 class="mb-0"><?= $lang["general"]['download_file_title'] ?></h5>
        </div>

        <div class="card-body p-0">
          <!-- File Preview Section -->
          <div class="p-4 text-center">
            <?php
            // Determine file type and show appropriate icon/preview
            $file_extension = pathinfo($file_data->filename, PATHINFO_EXTENSION);
            $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array(strtolower($file_extension), $image_extensions)) {
              // Show image preview for image files
                echo '<div class="file-preview mb-3"><img src="' . $file_data->directlink . '" class="img-fluid rounded" style="max-height: 200px;" alt="' . $file_data->filename . '"></div>';
            } else {
              // Show file type icon for non-image files
                echo '<div class="file-icon mb-3"><i class="fas fa-file fa-4x"></i></div>';
            }
            ?>
            <h4 class="text-break"><?= $file_data->filename ?></h4>
          </div>

          <!-- File Information -->
          <div class="container p-4">
            <div class="row g-4">
              <!-- File Details -->
              <div class="col-md-8">
                <div class="card h-100 border rounded-3">
                  <div class="card-body">
                    <h5 class="card-title border-bottom pb-2 mb-3"><?= $lang["general"]['file_details'] ?? 'File Details' ?></h5>
                    <div class="row">
                      <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-file-alt me-2"></i>
                          <div>
                            <small class="text-muted d-block"><?= $lang["general"]['file_name'] ?></small>
                            <span class="text-break"><?= $file_data->filename ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-weight-hanging me-2"></i>
                          <div>
                            <small class="text-muted d-block"><?= $lang["general"]['file_size'] ?></small>
                            <span><?= $file_data->filesize ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-calendar-alt me-2"></i>
                          <div>
                            <small class="text-muted d-block"><?= $lang["general"]['upload_date'] ?></small>
                            <span><?= $file_data->uploaddate ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-fingerprint me-2"></i>
                          <div>
                            <small class="text-muted d-block"><?= $lang["general"]['file_hash'] ?></small>
                            <span class="text-break small"><?= $file_data->filehash ?></span>
                          </div>
                        </div>
                      </div>
                      <?php if (isset($file_data->filemime)) : ?>
                      <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-file-code me-2"></i>
                          <div>
                            <small class="text-muted d-block"><?= $lang["general"]['file_type'] ?? 'File Type' ?></small>
                            <span><?= $file_data->filemime ?></span>
                          </div>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- QR Code -->
              <div class="col-md-4">
                <div class="card h-100 border rounded-3">
                  <div class="card-body text-center">
                    <h5 class="card-title border-bottom pb-2 mb-3"><?= $lang["general"]['scan_qr'] ?? 'Scan QR Code' ?></h5>
                    <img class="img-fluid rounded" src="<?= $file_data->qrcode ?>" alt="QR Code" style="max-width: 150px;" />
                    <p class="small text-muted mt-2"><?= $lang["general"]['scan_to_download'] ?? 'Scan to download' ?></p>
                  </div>
                </div>
              </div>
            </div>

            <?php if ($settings->getSettingValue('sharethis_status')) : ?>
              <div class="mt-4 p-3 border rounded-3">
                <h5 class="mb-2"><?= $lang["general"]['share_file'] ?? 'Share this file' ?></h5>
                <div class="sharethis-inline-share-buttons"></div>
              </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 mt-4">
              <a href="<?= $file_data->directlink ?>" class="btn btn-primary" download>
                <i class="fas fa-download me-2"></i><?= $lang["general"]['download_button'] ?>
              </a>
              <a href="reportabuse.php?file_id=<?= $file_data->file_id ?>" class="btn btn-outline-danger">
                <i class="fas fa-flag me-2"></i><?= $lang["general"]['report_abuse'] ?>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Download Stats (Optional) -->
      <?php if (isset($file_data->downloads)) : ?>
        <div class="card mt-3 shadow-sm border-0 rounded-3">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="fas fa-chart-bar me-2"></i>
              <span><?= $lang["general"]['download_count'] ?? 'Downloads' ?>: <?= $file_data->downloads ?></span>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include_once 'components/footer.php'; ?>