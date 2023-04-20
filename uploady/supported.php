<?php
include_once 'session.php';
include_once APP_PATH . 'logic/supportedLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div id="wrapper">
  <div id="content-wrapper">
    <div class="container pb-5 pt-5">
      <div class="card">
        <div class="card-header">
          <?= $lang['supported_formats'] ?>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="supported" width="100%" cellspacing="0">
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