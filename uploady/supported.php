<?php
include_once 'session.php';
include_once APP_PATH . 'logic/supportedLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <!-- Page Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0"><?= $lang["general"]['supported_formats'] ?></h2>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left me-1"></i> <?= $lang["general"]['back_to_home'] ?? 'Back to Home'; ?>
        </a>
      </div>
      
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header py-3">
          <h5 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            <?= $lang["general"]['supported_formats'] ?>
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="alert alert-info mb-4">
            <div class="d-flex">
              <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
              </div>
              <div>
                <h5 class="alert-heading"><?= $lang["general"]['file_upload_info'] ?? 'File Upload Information'; ?></h5>
                <p class="mb-0"><?= $lang["general"]['supported_formats_info'] ?? 'Below is a list of all file formats currently supported by our system. Files must be under the specified maximum size limit.'; ?></p>
              </div>
            </div>
          </div>
          
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="supported">
              <thead class="table-light">
                <tr>
                  <th scope="col" class="text-nowrap">
                    <i class="fas fa-file me-2"></i><?= $lang["general"]['format']; ?>
                  </th>
                  <th scope="col" class="text-nowrap">
                    <i class="fas fa-weight-hanging me-2"></i><?= $lang["general"]['max_size']; ?>
                  </th>
                  <th scope="col" class="text-nowrap">
                    <i class="fas fa-check-circle me-2"></i><?= $lang["general"]['status'] ?>
                  </th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($filter['extensions'] as $key => $value) : ?>
                  <tr>
                    <td>
                      <span class="badge bg-light text-dark me-2">.<?= $key ?></span>
                      <strong><?= $value ?? $key ?></strong>
                    </td>
                    <td><?= MAX_SIZE; ?></td>
                    <td>
                      <span class="badge bg-success">
                        <i class="fas fa-check me-1"></i><?= $lang["general"]['allowed'] ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Additional Info -->
          <div class="mt-4 pt-3 border-top">
            <h6><i class="fas fa-exclamation-triangle me-2"></i><?= $lang["general"]['please_note'] ?? 'Please Note'; ?>:</h6>
            <ul class="text-muted">
              <li><?= $lang["general"]['upload_limit_note'] ?? 'The maximum upload size may also be limited by your PHP configuration.'; ?></li>
              <li><?= $lang["general"]['browser_limit_note'] ?? 'Some browsers may have additional limitations on file uploads.'; ?></li>
              <li><?= $lang["general"]['contact_admin_note'] ?? 'If you need to upload a file type not listed here, please contact the administrator.'; ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Optional: Initialize a datatable for sorting/filtering if needed
    // if (typeof $.fn.DataTable !== 'undefined') {
    //   $('#supported').DataTable({
    //     "paging": false,
    //     "info": false
    //   });
    // }
  });
</script>

<?php include_once 'components/footer.php'; ?>