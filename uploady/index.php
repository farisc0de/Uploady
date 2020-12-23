<?php
include_once 'session.php';
include_once APP_PATH . 'logic/indexLogic.php';
?>
<!DOCTYPE html>
<html lang="en">
<html>

<head>
  <meta charset="UTF-8" />
  <?php include_once 'components/header.php'; ?>
  <title>
    <?= $st['website_name'] ?> - File Uploading Service
  </title>
  <?php include_once 'components/css.php'; ?>
</head>

<body>

  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="row justify-content-center text-center">
          <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="card">
              <div class="card-header">
                <b>Upload Your Files</b>
              </div>
              <div class="card-body">
                <div id="upload_alert"></div>
                <div id="upload_alert">
                  <?php if (isset($_GET['error'])) : ?>
                    <?= $utils->alert(
                      'You are trying to bypass the security measures',
                      'danger'
                    ); ?>
                  <?php endif; ?>
                </div>
                <p class="card-title lead font-weight-bold text-dark">
                  Select Files
                </p>
                <form enctype="multipart/form-data" role="form" method="POST" action="upload.php">
                  <div class="form-group" id="dvFile">
                    <input type="file" class="form-control-file pt-2" name="file[]" />
                    <input type="file" class="form-control-file pt-2" name="file[]" />
                    <input type="file" class="form-control-file pt-2" name="file[]" />
                    <input type="file" class="form-control-file pt-2" name="file[]" />
                  </div>

                  <div class="custom-control custom-checkbox m-3">
                    <input type="checkbox" class="custom-control-input" id="tos">
                    <label class="custom-control-label" for="tos">
                      I agree to the <a href="terms.php">Terms and Conditions</a>
                    </label>
                  </div>

                  <button id="submit" name="submit" type="submit" class="btn btn-primary" disabled>
                    <span class="fa fa-upload"></span> Upload Files
                  </button>
                  <button type="button" id="add_more" class="btn btn-primary text-white">
                    <span class="fa fa-plus"></span> Add a File
                  </button>
                </form>
              </div>
              <div class="card-footer mb-0">
                <p class="mb-0">
                  Note: Supported formats:
                  <a href="supported.php">See Here...</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>
</body>

</html>

</html>