<?php include_once 'session.php'; ?>

<?php include_once "logic/reportAbuseLogic.php"; ?>

<?php include_once 'components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><?= $lang["general"]['report_abuse_title'] ?></h2>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= $lang["general"]['back'] ?? 'Back'; ?>
                </a>
            </div>
            
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-flag me-2"></i>
                        <?= $lang["general"]['report_abuse_title'] ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_GET['msg'])) : ?>
                        <?php if ($_GET['msg'] == 'report_sent') : ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading"><?= $lang["general"]['thank_you'] ?? 'Thank You'; ?></h5>
                                    <p class="mb-0"><?= $lang["general"]['report_abuse_success']; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($_GET['msg'] == 'file_not_found') : ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <div class="me-3">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading"><?= $lang["general"]['error'] ?? 'Error'; ?></h5>
                                    <p class="mb-0"><?= $lang["general"]['report_file_not_found'] ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Report Abuse Info -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading"><?= $lang["general"]['report_abuse_info_title'] ?? 'Why Report Abuse?'; ?></h5>
                                <p class="mb-0"><?= $lang["general"]['report_abuse_info'] ?? 'If you believe this file contains illegal, harmful, or inappropriate content, please submit this form to report it to our team.'; ?></p>
                            </div>
                        </div>
                    </div>

                    <form method="post" action="actions/reportabuse.php">
                        <div class="mb-4">
                            <label for="emailaddress" class="form-label"><?= $lang["general"]['your_email'] ?? 'Your Email Address'; ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="emailaddress" name="emailaddress" 
                                    placeholder="name@example.com" required>
                            </div>
                            <div class="form-text">
                                <small><?= $lang["general"]['email_privacy_note'] ?? 'Your email will only be used to contact you regarding this report.'; ?></small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="fileid" class="form-label"><?= $lang["general"]['file_id'] ?? 'File ID'; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="text" class="form-control" id="fileid" name="fileid" 
                                    placeholder="<?= $lang["general"]['file_url_placeholder'] ?>" value="<?= $fileID ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="fileabusenote" class="form-label"><?= $lang["general"]['abuse_details'] ?? 'Abuse Details'; ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="fileabusenote" name="fileabusenote" rows="5" 
                                placeholder="<?= $lang["general"]['file_abuse_notes_placeholder'] ?>" required></textarea>
                            <div class="form-text">
                                <small><?= $lang["general"]['please_provide_details'] ?? 'Please provide as much detail as possible about why this file should be reported.'; ?></small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><?= $lang["general"]['abuse_type'] ?? 'Type of Abuse'; ?> <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abusetype" id="copyright" value="copyright" required>
                                <label class="form-check-label" for="copyright">
                                    <?= $lang["general"]['copyright_violation'] ?? 'Copyright Violation'; ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abusetype" id="illegal" value="illegal">
                                <label class="form-check-label" for="illegal">
                                    <?= $lang["general"]['illegal_content'] ?? 'Illegal Content'; ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abusetype" id="harmful" value="harmful">
                                <label class="form-check-label" for="harmful">
                                    <?= $lang["general"]['harmful_content'] ?? 'Harmful or Malicious Content'; ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abusetype" id="other" value="other">
                                <label class="form-check-label" for="other">
                                    <?= $lang["general"]['other'] ?? 'Other (Please specify in details)'; ?>
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane me-2"></i><?= $lang["general"]['report_abuse_button'] ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>