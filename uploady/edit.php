<?php include_once 'session.php'; ?>

<?php include_once 'logic/editFileLogic.php'; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <input type="hidden" id="file_name" value="<?= $file_data['filename'] ?>">

            <form id="delete_form" method="POST" action="actions/update_file.php?action=delete_settings">
                <input type="hidden" name="file_id" value="<?= $utils->sanitize($_GET['file_id']) ?>">
                <div class="pb-3">
                    <label for="delete_at_downloads" class="form-label"><?= $lang["general"]['delete_after_days'] ?></label>
                    <input type="text" id="delete_at_days" class="form-control" placeholder="<?= $lang['general']['delete_after_downloads_placeholder'] ?>" name="days" value="<?= $file_settings['delete_at']['days'] ?>">
                    <small class="text-muted"><?= $lang["general"]['delete_after_days_help'] ?></small>
                </div>

                <div class="pb-3">
                    <label for="delete_at_downloads" class="form-label"><?= $lang["general"]['delete_after_downloads'] ?></label>
                    <input type="text" id="delete_at_downloads" class="form-control" name="downloads" value="<?= $file_settings['delete_at']['downloads'] ?>">
                    <small class="text-muted"><?= $lang["general"]['delete_after_downloads_help'] ?></small>
                </div>

                <button type="submit" id="delete_at_btn" class="btn btn-primary"><?= $lang["general"]["set_btn"] ?></button>
            </form>

            <?php if (in_array($file_data['filemime'], $image_mime)) : ?>
                <div id="alert">
                </div>

                <img name="canvas" id="canvas" src="<?= $picture ?>"></img>

                <h4 class="text-center my-3"><?= $lang["general"]['filters_title'] ?></h4>

                <div class="row my-4 text-center">
                    <?php foreach ($filters as $filter) : ?>
                        <div class="col-md-3 mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn <?= strtolower($filter)  ?>-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled><?= $filter ?></button>
                                <button class="filter-btn <?= strtolower($filter)  ?>-add btn btn-primary">+</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- ./row -->

                <h4 class="text-center my-3"><?= $lang["general"]['effects_title'] ?></h4>

                <select class="form-select" id="effects">
                    <option value="none"><?= $lang["general"]['select_effect'] ?></option>
                    <?php foreach ($effects as $value => $name) : ?>
                        <option value="<?= $value ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="row mt-5">
                    <div class="col-md-6 mb-3">
                        <button id="saveImageToUploads" class="btn btn-primary"><?= $lang["general"]['save_image_btn'] ?></button>
                    </div>
                    <div class="col-md-6">
                        <button id="clearFilters" class="btn btn-danger"><?= $lang["general"]['remove_filter_btn'] ?></button>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>