<?php
include_once 'session.php';
include_once APP_PATH . 'logic/indexLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <!-- Welcome Section -->
      <div class="text-center mb-4">
        <h1 class="display-5 mb-3"><?= $lang["general"]['welcome_title'] ?? 'Welcome to Uploady'; ?></h1>
        <p class="lead"><?= $lang["general"]['welcome_subtitle'] ?? 'The simple way to share your files'; ?></p>
      </div>

      <!-- Upload Card -->
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header py-3">
          <h5 class="mb-0">
            <i class="fas fa-cloud-upload-alt me-2"></i>
            <?= $lang["general"]['upload_form_card_title']; ?>
          </h5>
        </div>
        <div class="card-body p-4">
          <input type="hidden" id="max_file_size" value="<?= $utility->convertUnit($utility->sizeInBytes(MAX_SIZE), "MB"); ?>">

          <!-- Upload Instructions -->
          <div class="alert alert-info mb-4">
            <div class="d-flex">
              <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
              </div>
              <div>
                <h5 class="alert-heading"><?= $lang["general"]['upload_instructions'] ?? 'How to Upload'; ?></h5>
                <p class="mb-0"><?= $lang["general"]['drag_drop_instructions'] ?? 'Drag and drop files here, or click to select files. Maximum file size: '; ?> <strong><?= MAX_SIZE; ?></strong></p>
              </div>
            </div>
          </div>

          <!-- Dropzone -->
          <form enctype="multipart/form-data" class="dropzone" id="my-dropzone" method="POST" action="actions/upload_file.php">
            <div class="fallback">
              <input name="file" type="file" multiple />
            </div>
          </form>

          <!-- Upload Progress -->
          <div id="upload-progress" class="mt-4 d-none">
            <h6 class="mb-2"><?= $lang["general"]['upload_progress'] ?? 'Upload Progress'; ?></h6>
            <div class="progress">
              <progress id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated w-100" value="0" max="100">0%</progress>
            </div>
            <div class="text-center mt-1 small" id="progress-text">0%</div>
          </div>
        </div>

        <div class="card-footer p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <i class="fas fa-info-circle me-1"></i>
              <span><?= $lang["general"]['note_supported_formats']; ?></span>
              <a href="supported.php" class="ms-1"><?= $lang["general"]['see_here']; ?></a>
            </div>
            <div>
              <?php if (!isset($_SESSION['loggedin'])) : ?>
                <a href="login.php" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-sign-in-alt me-1"></i> <?= $lang["general"]['login'] ?? 'Login'; ?>
                </a>
              <?php else : ?>
                <a href="profile/my_files.php" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-folder me-1"></i> <?= $lang["general"]['my_files'] ?? 'My Files'; ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div class="row mt-5 g-4">
        <div class="col-md-4">
          <div class="text-center p-3">
            <div class="mb-3">
              <i class="fas fa-lock fa-3x"></i>
            </div>
            <h5><?= $lang['features']['file_secure'] ?? 'Secure'; ?></h5>
            <p class="text-muted"><?= $lang['features']['file_secure_desc'] ?? 'Your files are encrypted and protected'; ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center p-3">
            <div class="mb-3">
              <i class="fas fa-bolt fa-3x"></i>
            </div>
            <h5><?= $lang['features']['file_fast'] ?? 'Fast'; ?></h5>
            <p class="text-muted"><?= $lang['features']['file_fast_desc'] ?? 'Upload and share files in seconds'; ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center p-3">
            <div class="mb-3">
              <i class="fas fa-user-shield fa-3x"></i>
            </div>
            <h5><?= $lang['features']['file_private'] ?? 'Private'; ?></h5>
            <p class="text-muted"><?= $lang['features']['file_private_desc'] ?? 'No registration required for basic uploads'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Dropzone with custom options
    Dropzone.options.myDropzone = {
      paramName: "file",
      maxFilesize: parseFloat(document.getElementById('max_file_size').value),
      acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar",
      dictDefaultMessage: `<i class="fas fa-cloud-upload-alt fa-3x mb-3"></i><br>${<?= json_encode($lang["general"]['dropzone_message'] ?? 'Drop files here or click to upload'); ?>}`,
      init: function() {
        this.on("addedfile", function(file) {
          document.getElementById('upload-progress').classList.remove('d-none');
        });

        this.on("uploadprogress", function(file, progress) {
          let progressBar = document.getElementById('upload-progress-bar');
          let progressText = document.getElementById('progress-text');
          let progressValue = Math.round(progress);
          progressBar.value = progressValue;
          progressText.textContent = progressValue + '%';
        });

        this.on("success", function(file, response) {
          // Handle successful upload
          console.log("Upload successful:", response);
        });

        this.on("error", function(file, errorMessage) {
          // Handle upload error
          console.error("Upload error:", errorMessage);
        });
      }
    };
  });
</script>

<?php include_once 'components/footer.php'; ?>