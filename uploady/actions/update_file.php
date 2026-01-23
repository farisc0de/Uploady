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

$utility = new Utility();
$role = new Uploady\Role($db, $user);
$handler = new UploadHandler($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (isset($_GET['action']) && $_GET['action'] == "delete_settings") {
        $handler->updateFileSettings(
            $_POST['file_id'],
            $_SESSION['user_id'],
            json_encode(["delete_at" => [
                "days" => $_POST['days'],
                "downloads" => $_POST['downloads'],
            ]])
        );

        $utils->redirect($utils->siteUrl("/edit.php?user_id=" . $_SESSION['user_id'] . "&file_id=" . $_POST['file_id'] . "&success=1"));
    }

    if (isset($_GET['action']) && $_GET['action'] == "edit_image") {
        try {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new \RuntimeException($lang["general"]['file_is_empty']);
            }

            $userRole = $role->get($_SESSION['user_role']);
            $sizeLimit = $userRole->size_limit ?? '50 MB';

            $userUploadDir = realpath("../" . UPLOAD_FOLDER) . '/' . $_SESSION['user_id'];
            if (!is_dir($userUploadDir)) {
                mkdir($userUploadDir, 0755, true);
            }

            $file = new File($_FILES['file'], $utility);
            
            $targetFilename = $_FILES['file']['name'];
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'tif'];
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/tiff'];
            
            $extension = strtolower(pathinfo($targetFilename, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) {
                throw new \RuntimeException($lang["general"]['file_type_is_not_allowed'] ?? 'File type not allowed');
            }
            
            $mimeType = $file->getMime();
            if (!in_array($mimeType, $allowedMimes)) {
                throw new \RuntimeException($lang["general"]['file_mime_type_is_not_allowed'] ?? 'MIME type not allowed');
            }
            
            $maxBytes = $utility->sizeInBytes($sizeLimit);
            if ($_FILES['file']['size'] > $maxBytes) {
                throw new \RuntimeException($lang["general"]['file_is_too_large'] ?? 'File is too large');
            }
            
            $targetPath = $userUploadDir . '/' . $targetFilename;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                chmod($targetPath, 0644);
                
                http_response_code(200);
                echo json_encode([
                    "success" => $lang["general"]['image_saved_success'],
                    "url" => SITE_URL . '/' . UPLOAD_FOLDER . '/' . $_SESSION['user_id'] . '/' . $targetFilename,
                ]);
            } else {
                throw new \RuntimeException($lang["general"]['upload_failed'] ?? 'Failed to save file');
            }

        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode([
                "error" => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "error" => $lang["general"]['upload_failed'] ?? 'Upload failed: ' . $e->getMessage(),
            ]);
        }
    }
}
