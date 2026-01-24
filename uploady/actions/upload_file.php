<?php

include_once '../session.php';

header("Content-type: application/json; charset=UTF-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use Uploady\Handler\UploadHandler;
use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\UploadManager;
use Farisc0de\PhpFileUploading\Storage\LocalStorage;
use Farisc0de\PhpFileUploading\Validation\ValidationChain;
use Farisc0de\PhpFileUploading\Validation\Validators\ExtensionValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\MimeTypeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\SizeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\FilenameValidator;
use Farisc0de\PhpFileUploading\Security\NullScanner;
use Farisc0de\PhpFileUploading\Security\ClamAvScanner;


$utility = new Utility();
$dataCollection = new Uploady\DataCollection();
$browser = new Wolfcast\BrowserDetection();
$role = new Uploady\Role($db, $user);
$handler = new UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new \RuntimeException($lang["general"]['file_is_empty']);
        }

        $userId = UploadManager::createUserId();
        $fileId = UploadManager::createFileId();

        $userRole = $role->get($_SESSION['user_role']);
        $sizeLimit = $userRole->size_limit ?? '1 GB';

        $userUploadDir = realpath("../" . UPLOAD_FOLDER) . '/' . $userId;
        if (!is_dir($userUploadDir)) {
            mkdir($userUploadDir, 0755, true);
        }

        $storage = new LocalStorage(
            $userUploadDir,
            0755,
            0644,
            SITE_URL . '/' . UPLOAD_FOLDER . '/' . $userId
        );

        $validator = new ValidationChain();
        $validator
            ->addValidator(new FilenameValidator([
                'b374k.php', 'c99.php', 'r57.php', 'wso.php', 'webadmin.php', 'weevely.php',
                'shell.php', 'backdoor.php', 'cmd.php', 'system.php', 'exec.php', 'alfa.php',
                'mini.php', 'bypass.php', 'rootkit.php', 'filesman.php', 'adminer.php', 'phpinfo.php',
                '.htaccess', '.htpasswd', 'config.php', 'wp-config.php', 'configuration.php'
            ]))
            ->addValidator(new ExtensionValidator([
                'tiff', 'tga', 'gif', 'psd', 'tif', 'bmp', 'png', 'jpeg', 'jpg', 'webp', 'svg',
                'ico', 'heic', 'heif', 'avif',
                'txt', 'json', 'xml', 'csv', 'md', 'markdown', 'rtf',
                'ttf', 'otf', 'woff', 'woff2',
                'tar.gz', 'gz', 'bz2', 'iso', 'torrent', '7z', 'ace', 'gtar', 'tar', 'zip', 'rar',
                'pps', 'ppt', 'pptx', 'docx', 'doc', 'pdf', 'xls', 'xlsx', 'odp', 'ods', 'odt',
                'epub', 'mobi',
                'ra', 'ram', 'rm', 'wma', '3ga', 'm4a', 'acc', 'oga', 'aac', 'ogg', 'mp3', 'wav', 'flac',
                'wmv', 'swf', 'flv', 'mov', 'm4v', '3g2', 'avi', '3gp', 'mp4', 'dat', 'mkv', 'mpg', 'mpeg', 'ogm',
                'apk'
            ]))
            ->addValidator(new MimeTypeValidator([
                'image/tiff', 'image/x-tga', 'image/gif', 'image/vnd.adobe.photoshop', 'image/bmp',
                'image/png', 'image/jpeg', 'image/webp', 'image/svg+xml', 'image/x-icon',
                'image/heic', 'image/heif', 'image/avif',
                'text/plain', 'text/csv', 'text/markdown',
                'font/ttf', 'font/otf', 'font/woff', 'font/woff2',
                'application/json', 'application/xml',
                'application/gzip', 'application/x-bzip2', 'application/x-iso9660-image',
                'application/x-bittorrent', 'application/x-7z-compressed', 'application/x-ace-compressed',
                'application/x-gtar', 'application/x-tar', 'application/zip', 'application/x-rar-compressed',
                'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword',
                'application/pdf', 'application/rtf',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'application/epub+zip', 'application/x-mobipocket-ebook',
                'audio/x-pn-realaudio', 'application/vnd.rn-realmedia', 'audio/x-ms-wma',
                'audio/3ga', 'audio/mp4', 'application/vnd.americandynamics.acc', 'audio/ogg',
                'audio/x-aac', 'audio/mpeg', 'audio/wav', 'audio/flac',
                'video/x-ms-wmv', 'application/x-shockwave-flash', 'video/x-flv', 'video/quicktime',
                'video/x-m4v', 'video/3gpp2', 'video/x-msvideo', 'video/3gpp', 'video/mp4',
                'zz-application/zz-winassoc-dat', 'video/x-matroska', 'video/mpeg', 'video/ogg',
                'application/vnd.android.package-archive'
            ]))
            ->addValidator(new SizeValidator(null, $sizeLimit));

        if($st['virus_scanner'] === '1') {
            $virusScanner = new ClamAvScanner('/var/run/clamav/clamd.sock');
        } else {
            $virusScanner = new NullScanner();
        }

        $uploadManager = new UploadManager(
            $storage,
            $validator,
            null,
            $virusScanner,
            null
        );

        $uploadManager->setHashFilenames(true);

        $uploadManager->setSiteUrl(SITE_URL);
        $uploadManager->setUserId($userId);
        $uploadManager->setFileId($fileId);

        $uploadManager->setBaseFolderName(UPLOAD_FOLDER . '/' . $userId);

        $file = new File($_FILES['file'], $utility);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $result = $uploadManager->upload($file, '', $clientIp);

        foreach ($result->getRateLimitHeaders() as $header => $value) {
            header("{$header}: {$value}");
        }

        if ($result->isSuccess()) {
            $fileData = [
                'filename' => $result->getOriginalFilename(),
                'filehash' => $result->getStoredFilename(),
                'filesize' => $utility->formatBytes($result->getFileSize()),
                'uploaddate' => date('Y-m-d H:i:s'),
                'fileurl' => $result->getPublicUrl(),
                'filetype' => $result->getMimeType(),
                'file_id' => $fileId,
                'user_id' => $userId,
                'sitename' => SITE_URL,
                'hash' => $result->getFileHash(),
                'qrcode' => $result->getQrCode(),
                'downloadlink' => $result->getDownloadLink(),
                'directlink' => $result->getDirectLink(),
                'deletelink' => $result->getDeleteLink(),
                'editlink' => $result->getEditLink(),
            ];

            $userData = [
                'ip_address' => $dataCollection->collectIP(),
                'country' => $dataCollection->idendifyCountry(),
                'browser' => $dataCollection->getBrowser($browser),
                'os' => $dataCollection->getOS()
            ];

            $fileSettings = [
                'delete_at' => [
                    'downloads' => 0,
                    'days' => 0,
                ],
            ];

            $handler->addFile(
                $uploadManager->getFileId(),
                $uploadManager->getUserId(),
                json_encode($fileData),
                json_encode($userData),
                json_encode($fileSettings)
            );

            http_response_code(200);
            echo json_encode($fileData);
        } else {
            http_response_code(400);
            echo json_encode([
                'error' => $result->getError(),
            ]);
        }

    } catch (\Farisc0de\PhpFileUploading\Exception\ValidationException $e) {
        http_response_code($e->getCode() === 1010 ? 429 : 400);
        echo json_encode([
            'error' => $e->getMessage(),
        ]);
    } catch (\RuntimeException $e) {
        http_response_code(400);
        echo json_encode([
            'error' => $e->getMessage(),
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => $lang["general"]['upload_failed'] ?? 'Upload failed: ' . $e->getMessage(),
        ]);
    }
}
