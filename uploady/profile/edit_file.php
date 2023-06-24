<?php

include '../session.php';

use Intervention\Image\ImageManager;
use Farisc0de\PhpFileUploading\Image;

$manager = new ImageManager(['driver' => 'imagick']);

$image = new Image();

$picture = $manager->make(APP_PATH . '/uploads/image.png')
    ->resize(300, 200)
    ->brightness(50)
    ->blur(3.5);

$img = $image->encodeImage($picture->encode("png"), "png");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <img src="<?= $img ?>" />
</body>

</html>