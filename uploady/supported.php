<?php
include_once 'session.php';
include_once APP_PATH . 'logic/supportedLogic.php';
?>

<?php include_once 'components/header.php'; ?>
<title><?= $st['website_name'] ?> - <?= $lang['supported_formats'] ?></title>
<?php include_once 'components/css.php'; ?>
<?php $utils->style(
  'https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css'
) ?>
</head>

<body>
  <?php include_once 'components/navbar.php'; ?>

  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container pb-5 pt-5">
        <div class="card">
          <div class="card-header">
            <?= $lang['supported_formats'] ?>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th><?= $lang['format']; ?></th>
                    <th><?= $lang['max_size']; ?></th>
                    <th><?= $lang['status'] ?></th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($filter['extensions'] as $key => $value) : ?>
                    <tr>
                      <td class="font-weight-bold"><?= $key ?></td>
                      <td class="font-weight-bold"><?= MAX_SIZE; ?></td>
                      <td class="font-weight-bold"><?= $lang['allowed'] ?></td>
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

  <?php $utils->script('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'); ?>
  <?php $utils->script(
    'https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js'
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