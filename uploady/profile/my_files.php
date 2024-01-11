<?php
include_once '../session.php';
include_once 'logic/myFilesLogic.php';
?>

<?php include_once '../components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="card">
                <div class="card-header text-center">
                    <b><?= $lang["general"]['my_files_title']; ?></b>
                </div>
                <form method="POST" action="actions/delete.php">
                    <?= $utils->input('csrf', $_SESSION['csrf']); ?>
                    <div class="card-body">
                        <?php if (isset($_GET['msg'])) : ?>

                            <?php if ($_GET['msg'] == "file_deleted") : ?>

                                <?php echo $utils->alert(
                                    $lang["general"]['delete_files_success'],
                                    "success",
                                    "check-circle"
                                ); ?>

                            <?php elseif ($_GET['msg'] == "csrf") : ?>

                                <?php echo $utils->alert(
                                    $lang["general"]['csrf_error'],
                                    "danger",
                                    "times-circle"
                                ); ?>

                            <?php endif; ?>

                        <?php endif; ?>
                        <div class="table-responsive pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                            <table class="table nowrap table-bordered" width="100%" id="dataTable" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="select-all" name="select-all">
                                                <label class="custom-control-label" for="select-all"></label>
                                            </div>
                                        </th>
                                        <th><?= $lang["general"]['file_name']; ?></th>
                                        <th><?= $lang["general"]['uploaded_at'] ?></th>
                                        <th><?= $lang["general"]['settings_title']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($files_info as $file) : ?>
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="<?= $file['file_id']; ?>" name="fileid[]" value="<?= $file['file_id']; ?>" />
                                                    <label class="custom-control-label" for="<?= $file['file_id']; ?>"></label>
                                                </div>
                                            </td>
                                            <td><?= $file['filename']; ?></td>
                                            <td><?= $file['uploaddate']; ?></td>
                                            <td class="text-center">
                                                <ul class="list-inline">
                                                    <li class="list-inline-item">
                                                        <a href="<?= $file['downloadlink']  ?>">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="<?= $file['editlink'] ?>">
                                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="<?= $file['deletelink']  ?>">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer mb-0 text-left">
                        <button class="btn btn-primary">
                            <?= $lang["general"]['delete_selected_btn']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../components/footer.php'; ?>