<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; <?= $st['website_name']; ?> <?= date("Y"); ?></div>
            <div>
                <a href="<?= $utils->siteUrl('/page.php?s=privacy'); ?>">Privacy Policy</a>
                &middot;
                <a href="<?= $utils->siteUrl('/page.php?s=terms'); ?>">
                    Terms &amp; Conditions
                </a>
            </div>
        </div>
    </div>
</footer>