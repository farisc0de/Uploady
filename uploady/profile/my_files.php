<?php
include_once '../session.php';
include_once 'logic/myFilesLogic.php';
?>

<?php include_once '../components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><?= $lang["general"]['my_files_title']; ?></h2>
                <div>
                    <button type="button" class="btn btn-outline-primary" id="upload-new-btn">
                        <i class="fas fa-cloud-upload-alt me-2"></i><?= $lang["general"]['upload_new'] ?? 'Upload New File'; ?>
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['msg'])) : ?>
                <div class="mb-4">
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
                </div>
            <?php endif; ?>

            <form method="POST" action="actions/delete.php">
                <?= $utils->input('csrf', $_SESSION['csrf']); ?>
                
                <!-- Search and Filter Bar -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6 col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="file-search" placeholder="<?= $lang["general"]['search_files'] ?? 'Search files...' ?>">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-8 text-md-end">
                                <button type="submit" class="btn btn-danger" id="delete-selected">
                                    <i class="fas fa-trash-alt me-2"></i><?= $lang["general"]['delete_selected_btn']; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files List -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="dataTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="select-all" name="select-all">
                                                <label class="form-check-label" for="select-all"></label>
                                            </div>
                                        </th>
                                        <th><?= $lang["general"]['file_name']; ?></th>
                                        <th><?= $lang["general"]['uploaded_at'] ?></th>
                                        <th class="text-end"><?= $lang["general"]['settings_title']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($files_info)) : ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                                    <p class="mb-0"><?= $lang["general"]['no_files'] ?? 'No files found' ?></p>
                                                    <p class="text-muted small"><?= $lang["general"]['upload_first'] ?? 'Upload your first file to get started' ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($files_info as $file) : ?>
                                            <?php 
                                            // Determine file type icon
                                            $file_extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
                                            $icon_class = 'fa-file';
                                            
                                            // Map extensions to Font Awesome icons
                                            $icon_map = [
                                                'pdf' => 'fa-file-pdf',
                                                'doc' => 'fa-file-word', 'docx' => 'fa-file-word',
                                                'xls' => 'fa-file-excel', 'xlsx' => 'fa-file-excel',
                                                'ppt' => 'fa-file-powerpoint', 'pptx' => 'fa-file-powerpoint',
                                                'jpg' => 'fa-file-image', 'jpeg' => 'fa-file-image', 'png' => 'fa-file-image', 'gif' => 'fa-file-image',
                                                'zip' => 'fa-file-archive', 'rar' => 'fa-file-archive', '7z' => 'fa-file-archive',
                                                'mp3' => 'fa-file-audio', 'wav' => 'fa-file-audio',
                                                'mp4' => 'fa-file-video', 'avi' => 'fa-file-video', 'mov' => 'fa-file-video',
                                                'html' => 'fa-file-code', 'css' => 'fa-file-code', 'js' => 'fa-file-code', 'php' => 'fa-file-code',
                                                'txt' => 'fa-file-alt'
                                            ];
                                            
                                            if (isset($icon_map[strtolower($file_extension)])) {
                                                $icon_class = $icon_map[strtolower($file_extension)];
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="<?= $file['file_id']; ?>" name="fileid[]" value="<?= $file['file_id']; ?>" />
                                                        <label class="form-check-label" for="<?= $file['file_id']; ?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="file-icon me-3">
                                                            <i class="fas <?= $icon_class ?> fa-lg"></i>
                                                        </div>
                                                        <div class="file-info">
                                                            <a href="<?= $file['downloadlink'] ?>" class="text-decoration-none">
                                                                <span class="d-block text-truncate" style="max-width: 250px;"><?= $file['filename']; ?></span>
                                                            </a>
                                                            <?php if (isset($file['filesize'])) : ?>
                                                                <small><?= $file['filesize']; ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span><?= $file['uploaddate']; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="<?= $file['downloadlink'] ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="<?= $lang["general"]['download'] ?? 'Download' ?>">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="<?= $file['editlink'] ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="<?= $lang["general"]['edit'] ?? 'Edit' ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= $file['deletelink'] ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="<?= $lang["general"]['delete'] ?? 'Delete' ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Handle select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="fileid[]"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = document.getElementById('select-all').checked;
            });
        });

        // Handle search functionality
        document.getElementById('file-search').addEventListener('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            var rows = document.querySelectorAll('#dataTable tbody tr');
            
            rows.forEach(function(row) {
                var fileName = row.querySelector('.file-info span').textContent.toLowerCase();
                if (fileName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Redirect to upload page when clicking the upload button
        document.getElementById('upload-new-btn').addEventListener('click', function() {
            window.location.href = '../index.php';
        });
    });
</script>

<?php include_once '../components/footer.php'; ?>