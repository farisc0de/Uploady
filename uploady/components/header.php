<!DOCTYPE html>
<html <?= $dir ?> class="h-100" data-bs-theme="<?= $theme ?>">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-language" content="<?= $language ?>" />
    <meta name="description" content="<?= $st['description'] ?>" />
    <meta name="author" content="<?= $st['owner_name'] ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="application-name" content="Uploady" />
    <link rel="shortcut icon" type="image/png" href="<?= $st['website_favicon']; ?>" />
    <meta name="keywords" content="<?= $st['keywords'] ?>" />
    <meta name="language" content="EN" />

    <title>
        <?= $st['website_name'] ?> - <?= $title ?>
    </title>
    <?php include_once APP_PATH . 'components/css.php'; ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include_once APP_PATH . 'components/navbar.php'; ?>