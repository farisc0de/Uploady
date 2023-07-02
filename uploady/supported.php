<?php
include_once 'session.php';
include_once APP_PATH . 'logic/supportedLogic.php';
?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
  <div class="card">
    <div class="card-header">
      <?= $lang["general"]['supported_formats'] ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="supported" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th><?= $lang["general"]['format']; ?></th>
              <th><?= $lang["general"]['max_size']; ?></th>
              <th><?= $lang["general"]['status'] ?></th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($filter['extensions'] as $key => $value) : ?>
              <tr>
                <td class="font-weight-bold"><?= $key ?></td>
                <td class="font-weight-bold"><?= MAX_SIZE; ?></td>
                <td class="font-weight-bold"><?= $lang["general"]['allowed'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once 'components/footer.php'; ?>