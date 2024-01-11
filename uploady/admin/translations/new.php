<?php include_once '../session.php'; ?>
<?php include_once './logic/new.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>View Translations - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../components/navbar.php' ?>
    <div id="layoutSidenav">
        <?php include_once '../components/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Add Translation
                        </div>
                        <div class="card-body">
                            <form method="POST" action="./actions/new.php">

                                <?= $utils->input('csrf', $_SESSION['csrf']); ?>

                                <div class="form-group">
                                    <select label="Select Language" name="lang_id" id="lang_id" class="form-control custom-select">
                                        <option>Select language</option>
                                        <?php foreach ($langs as $lang) : ?>
                                            <option value="<?= $lang->id ?>"><?= $lang->language ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select label="Select a page to translate" name="page_id" id="page_id" class="form-control custom-select">
                                        <option>Select a page to translate</option>
                                        <?php foreach ($pages as $page) : ?>
                                            <option value="<?= $page->id ?>"><?= $page->slug ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="form-label-group">
                                        <input class="form-control" type="text" id="title" name="title" placeholder="Page title">
                                        <label for="page_title">Page title</label>
                                    </div>
                                </div>

                                <textarea id="page_content" name="content" class="form-control" placeholder="Page content"></textarea>

                                <div class="form-group mt-3">
                                    <button class="btn btn-primary" type="submit">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
    <?php $utils->script("js/tinymce/tinymce.min.js", "admin/assets") ?>
    <script>
        tinymce.init({
            selector: 'textarea#page_content',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    </script>
</body>

</html>