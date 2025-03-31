<?php
include_once '../session.php';
include_once 'logic/accountLogic.php';
?>
<?php include_once '../components/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0"><?= $lang["general"]['edit_account_title']; ?></h2>
            </div>

            <?php if (isset($_GET['msg'])) : ?>
                <div class="mb-4">
                    <?php if ($_GET['msg'] == "profile_updated") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['edit_account_success'],
                            "success",
                            "check-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "csrf") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['csrf_error'],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "error") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['edit_account_error'],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php elseif ($_GET['msg'] == "attack") : ?>
                        <?php echo $utils->alert(
                            $lang["general"]['attack_error'],
                            "danger",
                            "times-circle"
                        ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Account Settings -->
                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                <?= $lang["general"]['account_settings'] ?? 'Account Settings'; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="actions/update.php">
                                <?= $utils->input('id', $data->id); ?>
                                <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                                <div class="mb-4">
                                    <label for="username" class="form-label"><?= $lang["general"]['username'] ?? 'Username'; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="<?= $lang["general"]['enter_username'] ?>" value="<?= $data->username; ?>">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label"><?= $lang["general"]['email'] ?? 'Email'; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="<?= $lang["general"]['enter_your_email'] ?>" value="<?= $data->email; ?>">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label"><?= $lang["general"]['password'] ?? 'Password'; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="<?= $lang["general"]['enter_password']; ?>">
                                    </div>
                                    <div class="form-text">
                                        <small><?= $lang["general"]['keep_empty_msg']; ?></small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="api_key" class="form-label"><?= $lang["general"]['api_key'] ?? 'API Key'; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control" id="api_key" name="api_key" value="<?= $data->api_key; ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copy-api-key" data-bs-toggle="tooltip" title="<?= $lang["general"]['copy_to_clipboard'] ?? 'Copy to clipboard'; ?>">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i><?= $lang["general"]['edit_account_btn']; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="col-lg-4 col-md-12">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                <?= $lang["general"]['security'] ?? 'Security'; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-3">
                                <a href="auth.php" class="btn btn-outline-primary">
                                    <i class="fas fa-lock me-2"></i><?= $lang["general"]['enable_two_factor'] ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header py-3 text-white bg-danger">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $lang["general"]['danger_zone'] ?? 'Danger Zone'; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted mb-3"><?= $lang["general"]['delete_account_warning'] ?? 'This action cannot be undone. All your data will be permanently deleted.'; ?></p>
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-danger" onclick="deleteAccount('<?= $_SESSION['csrf'] ?>')">
                                    <i class="fas fa-trash-alt me-2"></i><?= $lang["general"]['delete_account']; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy API Key Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Copy API key to clipboard
        document.getElementById('copy-api-key').addEventListener('click', function() {
            var apiKeyInput = document.getElementById('api_key');
            apiKeyInput.select();
            document.execCommand('copy');
            
            // Update tooltip
            var tooltip = bootstrap.Tooltip.getInstance(this);
            this.setAttribute('data-bs-original-title', '<?= $lang["general"]['copied'] ?? 'Copied!' ?>');
            if (tooltip) tooltip.update();
            
            // Reset tooltip after 2 seconds
            setTimeout(function() {
                document.getElementById('copy-api-key').setAttribute('data-bs-original-title', '<?= $lang["general"]['copy_to_clipboard'] ?? 'Copy to clipboard' ?>');
                if (tooltip) tooltip.update();
            }, 2000);
        });
    });

    // Delete account function
    function deleteAccount(csrf) {
        if (confirm('<?= $lang["general"]['delete_account_confirm'] ?? 'Are you sure you want to delete your account? This action cannot be undone.' ?>')) {
            window.location.href = 'actions/delete_account.php?csrf=' + csrf;
        }
    }
</script>

<?php include_once '../components/footer.php'; ?>