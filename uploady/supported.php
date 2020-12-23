<?php
include_once 'session.php';
include_once APP_PATH . 'logic/supportedLogic.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <?php include_once 'components/header.php'; ?>
  <title><?= $st['website_name'] ?> - Supported Formats</title>
  <?php include_once 'components/css.php'; ?>
  <?php $utils->style(
    'https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css'
  ) ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="card">
          <div class="card-header">
            Supported Formats
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Format</th>
                    <th>Maximum Size</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($filter as $key => $value) : ?>
                    <tr>
                      <td class="font-weight-bold"><?= $key ?></td>
                      <td class="font-weight-bold">100 MB</td>
                      <td class="font-weight-bold">Allowed</td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once 'components/footer.php'; ?>

  <?php include_once 'components/js.php'; ?>

  <?php $utils->script(
    'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'
  ); ?>
  <?php $utils->script(
    'https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js'
  ); ?>

  <script>
    $(document).ready(function() {
      var table = $("#dataTable").DataTable({
        ordering: true,

        select: {
          style: "multi",
        },
        order: [
          [0, "asc"]
        ],
        columnDefs: [{
          targets: 0,
          orderable: false,
        }, ],
      });
    });
  </script>
</body>

</html>