<?php include_once 'session.php'; ?>

<?php include_once 'logic/editFileLogic.php'; ?>

<?php include_once 'components/header.php'; ?>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><?= $lang["general"]['edit_file_title'] ?? 'Edit File'; ?></h2>
                <a href="profile/my_files.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= $lang["general"]['back_to_files'] ?? 'Back to My Files'; ?>
                </a>
            </div>

            <input type="hidden" id="file_name" value="<?= $file_data['filename'] ?>">

            <!-- File Info Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        <?= $lang["general"]['file_settings'] ?? 'File Settings'; ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="delete_form" method="POST" action="actions/update_file.php?action=delete_settings">
                        <input type="hidden" name="file_id" value="<?= $utils->sanitize($_GET['file_id']) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delete_at_days" class="form-label"><?= $lang["general"]['delete_after_days'] ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="number" id="delete_at_days" class="form-control"
                                        placeholder="<?= $lang['general']['delete_after_downloads_placeholder'] ?>"
                                        name="days" value="<?= $file_settings['delete_at']['days'] ?>" min="0">
                                </div>
                                <div class="form-text">
                                    <small><?= $lang["general"]['delete_after_days_help'] ?></small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="delete_at_downloads" class="form-label"><?= $lang["general"]['delete_after_downloads'] ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-download"></i></span>
                                    <input type="number" id="delete_at_downloads" class="form-control"
                                        name="downloads" value="<?= $file_settings['delete_at']['downloads'] ?>" min="0">
                                </div>
                                <div class="form-text">
                                    <small><?= $lang["general"]['delete_after_downloads_help'] ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-md-4 mx-auto mt-3">
                            <button type="submit" id="delete_at_btn" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= $lang["general"]["set_btn"] ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (in_array($file_data['filemime'], $image_mime)) : ?>
                <div id="alert" class="mb-4"></div>

                <!-- Image Editor Card -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            <?= $lang["general"]['image_editor'] ?? 'Image Editor'; ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Image Preview -->
                        <div class="text-center mb-4">
                            <div class="img-container mb-3 p-2 border rounded">
                                <img id="canvas" src="<?= $picture ?>" class="img-fluid rounded" alt="<?= $file_data['filename'] ?>"></img>
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3"><?= $lang["general"]['filters_title'] ?></h5>
                            <div class="row g-3 mb-3">
                                <?php foreach ($filters as $filter) : ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2"><?= $filter ?></span>
                                            <div class="btn-group ms-auto">
                                                <button class="filter-btn <?= strtolower($filter) ?>-remove btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button class="filter-btn <?= strtolower($filter) ?>-add btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Effects Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3"><?= $lang["general"]['effects_title'] ?></h5>
                            <div class="row">
                                <div class="col-md-6 mx-auto">
                                    <select class="form-select" id="effects">
                                        <option value="none"><?= $lang["general"]['select_effect'] ?></option>
                                        <?php foreach ($effects as $value => $name) : ?>
                                            <option value="<?= $value ?>"><?= $name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button id="saveImageToUploads" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= $lang["general"]['save_image_btn'] ?>
                            </button>
                            <button id="clearFilters" class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt me-2"></i><?= $lang["general"]['remove_filter_btn'] ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>