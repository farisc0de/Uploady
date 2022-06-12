<?php
include '../src/Utility.php';
include '../src/Upload.php';
include '../src/File.php';

use Farisc0de\PhpFileUploading\Upload;
use Farisc0de\PhpFileUploading\File;

$upload = new Upload();

$upload->setController('../src/');

$upload->setUploadFolder([
    'folder_name' => 'uploads',
    'folder_path' => realpath('uploads')
]);

$upload->enableProtection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $files = $util->fixArray($_FILES['file']);

    foreach ($files as $file) {
        $upload->setUpload(new File($file));

        if ($upload->checkIfNotEmpty()) {
            if (!$upload->checkForbidden()) {
                echo "Forbidden name";
                exit;
            }

            if (!$upload->checkExtension()) {
                echo "Forbidden Extension";
                exit;
            }

            if (!$upload->checkMime()) {
                echo "Forbidden Mime";
                exit;
            }

            if (!$upload->isImage()) {
                echo "Not Image";
                exit;
            }

            $upload->upload();
        }
    }

    $data = $upload->getFiles();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Uploading Service</title>
</head>

<body>
    <?php if (isset($data)) : ?>
        <p>You file has been uploaded</p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div><input type="file" name="file[]"></div>
        <div><input type="file" name="file[]"></div>
        <div><input type="file" name="file[]"></div>
        <button type="submit">Upload</button>
    </form>

    <div>
        <ul>
            <?php if (isset($data)) : ?>
                <?php foreach ($data as $d) : ?>
                    <li>Filename: <?= $d['filename']; ?></li>
                    <li>Filehash: <?= $d['filehash']; ?></li>
                    <li>Filesize: <?= $d['filesize']; ?></li>
                    <li>Upload at: <?= $d['uploaddate']; ?></li>
                    <br />
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>

</html>