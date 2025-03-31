<?php

namespace Farisc0de\PhpFileUploading;

use RuntimeException;
use InvalidArgumentException;
use Exception;

/**
 * PHP Library to help you build your own file sharing website.
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */

final class Upload
{
    private ?File $file;
    private Utility $util;
    private array $name_array = [];
    private array $filter_array = [];
    private array $upload_folder = [];
    private int $size;
    private string $file_name;
    private string $hash_id;
    private ?string $file_id;
    private ?string $user_id;
    private array $logs = [];
    private array $files = [];
    private ?int $max_height;
    private ?int $max_width;
    private ?int $min_height;
    private ?int $min_width;
    private string $site_url;
    private bool $is_hashed = false;

    private const ALLOWED_IMAGE_MIMES = [
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/png'
    ];

    private const ERROR_MESSAGES = [
        0 => "File has been uploaded.",
        1 => "Invalid file format.",
        2 => "Failed to get MIME type.",
        3 => "File is forbidden.",
        4 => "Exceeded filesize limit.",
        5 => "Please select a file",
        6 => "File already exists.",
        7 => "Failed to move uploaded file.",
        8 => "The uploaded file's height is too large.",
        9 => "The uploaded file's width is too large.",
        10 => "The uploaded file's height is too small.",
        11 => "The uploaded file's width is too small.",
        12 => "The uploaded file's is too small.",
        13 => "The uploaded file is not a valid image.",
        14 => "Operation does not exist."
    ];

    public function __construct(
        Utility $util,
        ?File $file = null,
        array $upload_folder = [],
        string $site_url = '',
        string $size = "5 GB",
        ?int $max_height = null,
        ?int $max_width = null,
        ?int $min_height = null,
        ?int $min_width = null,
        ?string $file_id = null,
        ?string $user_id = null
    ) {
        $this->validateConstructorParams($upload_folder, $site_url, $size);

        $this->util = $util;
        $this->file = $file;
        $this->upload_folder = $upload_folder;
        $this->site_url = $this->util->sanitize($site_url);
        $this->size = $this->util->sizeInBytes($this->util->sanitize($size));
        $this->max_height = $max_height;
        $this->max_width = $max_width;
        $this->min_height = $min_height;
        $this->min_width = $min_width;
        $this->file_id = $file_id;
        $this->user_id = $user_id;

        // Initialize file_name and hash_id if file is provided
        if ($this->file !== null) {
            $this->file_name = $this->file->getName();
            $this->hash_id = $this->file->getFileHash();
        }
    }

    private function validateConstructorParams(array $upload_folder, string $site_url, string $size): void
    {
        if (!empty($upload_folder) && (!isset($upload_folder['folder_name']) || !isset($upload_folder['folder_path']))) {
            throw new InvalidArgumentException('Upload folder array must contain folder_name and folder_path keys');
        }

        if (!empty($site_url) && !filter_var($site_url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid site URL provided');
        }

        if (!preg_match('/^\d+\s*(?:B|KB|MB|GB|TB)$/i', $size)) {
            throw new InvalidArgumentException('Invalid size format. Expected format: number followed by B/KB/MB/GB/TB');
        }
    }

    public function setUpload(File $file): void
    {
        $this->file = $file;
        $this->file_name = $file->getName();
        $this->hash_id = $file->getFileHash();
    }

    public function enableProtection(): void
    {
        $filterPath = realpath(__DIR__) . DIRECTORY_SEPARATOR . "filter.json";

        if (!file_exists($filterPath)) {
            throw new RuntimeException('Filter configuration file not found');
        }

        $filterContent = file_get_contents($filterPath);
        if ($filterContent === false) {
            throw new RuntimeException('Unable to read filter configuration');
        }

        $filters = json_decode($filterContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid filter configuration format');
        }

        $this->name_array = $filters['forbidden'] ?? [];
        $this->filter_array = $filters['extensions'] ?? [];
    }

    public function setForbiddenFilter(array $forbidden_array): void
    {
        if (empty($forbidden_array)) {
            throw new InvalidArgumentException('Forbidden array cannot be empty');
        }
        $this->name_array = array_map([$this->util, 'sanitize'], $forbidden_array);
    }

    public function setProtectionFilter(array $filter_array): void
    {
        if (empty($filter_array)) {
            throw new InvalidArgumentException('Filter array cannot be empty');
        }
        $this->filter_array = array_map([$this->util, 'sanitize'], $filter_array);
    }

    public function setUploadFolder(array $upload_folder): void
    {
        if (!isset($upload_folder['folder_name']) || !isset($upload_folder['folder_path'])) {
            throw new InvalidArgumentException('Upload folder array must contain folder_name and folder_path keys');
        }

        $this->upload_folder = $upload_folder;
    }

    public function setSizeLimit(string $size): void
    {
        if (!preg_match('/^\d+\s*(?:B|KB|MB|GB|TB)$/i', $size)) {
            throw new InvalidArgumentException('Invalid size format. Expected format: number followed by B/KB/MB/GB/TB');
        }
        $this->size = $this->util->sizeInBytes($this->util->sanitize($size));
    }

    public function checkSize(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if ($this->file->getSize() > $this->size) {
            $this->addLog(['filename' => $this->file_name, "message" => 4]);
            return false;
        }

        return true;
    }

    public function checkDimension(int $operation = 2): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if (!$this->isImage()) {
            throw new RuntimeException('File is not an image');
        }

        $image_data = @getimagesize($this->file->getTempName());
        if ($image_data === false) {
            throw new RuntimeException('Unable to get image dimensions');
        }

        [$width, $height] = $image_data;

        switch ($operation) {
            case 0:
                if ($this->max_height && $height > $this->max_height) {
                    $this->addLog(['filename' => $this->file_name, "message" => 8]);
                    return false;
                }
                break;

            case 1:
                if ($this->max_width && $width > $this->max_width) {
                    $this->addLog(['filename' => $this->file_name, "message" => 9]);
                    return false;
                }
                break;

            case 2:
                if (
                    $this->max_width && $this->max_height &&
                    ($width > $this->max_width || $height > $this->max_height)
                ) {
                    $this->addLog(['filename' => $this->file_name, "message" => 8]);
                    return false;
                }
                break;

            case 3:
                if ($this->min_height && $height < $this->min_height) {
                    $this->addLog(['filename' => $this->file_name, "message" => 10]);
                    return false;
                }
                break;

            case 4:
                if ($this->min_width && $width < $this->min_width) {
                    $this->addLog(['filename' => $this->file_name, "message" => 11]);
                    return false;
                }
                break;

            case 5:
                if (
                    $this->min_width && $this->min_height &&
                    ($width < $this->min_width || $height < $this->min_height)
                ) {
                    $this->addLog(['filename' => $this->file_name, "message" => 12]);
                    return false;
                }
                break;

            default:
                $this->addLog(['filename' => $this->file_name, "message" => 14]);
                throw new InvalidArgumentException('Invalid operation code');
        }

        return true;
    }

    public function isImage(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if (!in_array($this->file->getMime(), self::ALLOWED_IMAGE_MIMES, true)) {
            $this->addLog(['filename' => $this->file_name, "message" => 13]);
            return false;
        }

        return true;
    }

    public function upload(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if (!$this->checkIfNotEmpty()) {
            return false;
        }

        try {
            $this->validateUpload();

            // Ensure file_name and hash_id are set
            if (empty($this->file_name)) {
                $this->file_name = $this->file->getName();
            }

            if (empty($this->hash_id)) {
                $this->hash_id = $this->file->getFileHash();
            }

            // If file has been hashed but the hash doesn't match, update it
            if ($this->file->getFileHash() !== $this->hash_id && !$this->is_hashed) {
                $this->file_name = $this->file->getName();
                $this->hash_id = $this->file->getFileHash();
            }

            $filename = $this->file_name;

            if ($this->moveFile($filename)) {
                $this->addLog(['filename' => $this->file_name, "message" => 0]);
                $this->addFile($this->getJSON());
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->addLog(['filename' => $this->file_name ?? 'unknown', "message" => $e->getMessage()]);
            return false;
        }
    }

    private function validateUpload(): void
    {
        if (empty($this->upload_folder)) {
            throw new RuntimeException('Upload folder not set');
        }

        if (!is_dir($this->upload_folder['folder_path'])) {
            throw new RuntimeException('Upload folder does not exist');
        }

        if (!is_writable($this->upload_folder['folder_path'])) {
            throw new RuntimeException('Upload folder is not writable');
        }
    }

    public function moveFile(string $filename): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        $this->disableTimeLimit();

        try {
            $targetPath = $this->upload_folder['folder_path'] . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($targetPath)) {
                $this->addLog(['filename' => $filename, "message" => 6]);
                return false;
            }

            return $this->moveFileInChunks($targetPath);
        } catch (Exception $e) {
            $this->addLog(['filename' => $filename, "message" => $e->getMessage()]);
            return false;
        }
    }

    private function moveFileInChunks(string $targetPath): bool
    {
        $chunk_size = 4096;
        $handle = @fopen($this->file->getTempName(), "rb");
        $fp = @fopen($targetPath, 'wb');

        if (!$handle || !$fp) {
            throw new RuntimeException('Failed to open file streams');
        }

        try {
            while (!feof($handle)) {
                $contents = fread($handle, $chunk_size);
                if ($contents === false) {
                    throw new RuntimeException('Failed to read from source file');
                }
                if (fwrite($fp, $contents) === false) {
                    throw new RuntimeException('Failed to write to target file');
                }
            }
        } finally {
            fclose($handle);
            if (is_resource($fp)) {
                if (!fclose($fp)) {
                    throw new RuntimeException('Failed to close target file');
                }
            }
        }

        return true;
    }

    private function disableTimeLimit(): void
    {
        if (strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
            @set_time_limit(0);
        }
    }

    public function createUserCloud(?string $main_upload_folder = null): bool
    {
        $user_id = $this->getUserID();
        if ($user_id === null) {
            throw new RuntimeException('User ID not set');
        }

        $upload_folder = $main_upload_folder ?? $this->upload_folder['folder_path'] ?? null;

        if ($upload_folder === null) {
            throw new RuntimeException('Upload folder not set');
        }

        $user_cloud = $upload_folder . DIRECTORY_SEPARATOR . $user_id;

        if (!file_exists($user_cloud)) {
            if (!@mkdir($user_cloud, 0755, true)) {
                throw new RuntimeException('Failed to create user cloud directory');
            }
        }

        return true;
    }

    public function getUserCloud(?string $main_upload_folder = null): string
    {
        $user_id = $this->getUserID();
        if ($user_id === null) {
            throw new RuntimeException('User ID not set');
        }

        $upload_folder = $main_upload_folder ?? $this->upload_folder['folder_path'] ?? null;

        if ($upload_folder === null) {
            throw new RuntimeException('Upload folder not set');
        }

        return $upload_folder . DIRECTORY_SEPARATOR . $user_id;
    }

    public function checkExtension(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if (empty($this->filter_array)) {
            throw new RuntimeException('Filter array not set');
        }

        if (!isset($this->filter_array[$this->file->getExtension()])) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);
            return false;
        }

        return true;
    }

    public function checkMime(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if (empty($this->filter_array)) {
            throw new RuntimeException('Filter array not set');
        }

        $mime = mime_content_type($this->file->getTempName());
        if ($mime === false) {
            $this->addLog(['filename' => $this->file_name, "message" => 2]);
            return false;
        }

        if (
            $this->filter_array[$this->file->getExtension()] !== $mime ||
            $mime !== $this->file->getMime()
        ) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);
            return false;
        }

        return true;
    }

    public function checkForbidden(): bool
    {
        if (empty($this->file_name)) {
            throw new RuntimeException('File name not set');
        }

        if (empty($this->name_array)) {
            return true; // No forbidden names set, so all names are allowed
        }

        if (in_array($this->file_name, $this->name_array, true)) {
            $this->addLog(['filename' => $this->file_name, "message" => 3]);
            return false;
        }

        return true;
    }

    public function checkIfNotEmpty(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        if ($this->file->isEmpty()) {
            $this->addLog(['filename' => $this->file_name, "message" => 5]);
            return false;
        }

        return true;
    }

    public function generateQrCode(): string
    {
        if (empty($this->site_url)) {
            throw new RuntimeException('Site URL not set');
        }

        return sprintf(
            "https://quickchart.io/qr?text=%s&size=150",
            urlencode($this->generateDownloadLink())
        );
    }

    public function generateDownloadLink(): string
    {
        if (empty($this->site_url)) {
            throw new RuntimeException('Site URL not set');
        }

        if (!$this->file_id) {
            throw new RuntimeException('File ID not set');
        }

        return sprintf(
            "%s/%s?file_id=%s",
            rtrim($this->site_url, '/'),
            "download.php",
            urlencode($this->file_id)
        );
    }

    public function generateDeleteLink(): string
    {
        if (empty($this->site_url)) {
            throw new RuntimeException('Site URL not set');
        }

        if (!$this->file_id || !$this->user_id) {
            throw new RuntimeException('File ID or User ID not set');
        }

        return sprintf(
            "%s/%s?file_id=%s&user_id=%s",
            rtrim($this->site_url, '/'),
            "delete.php",
            urlencode($this->file_id),
            urlencode($this->user_id)
        );
    }

    public function generateEditLink(): string
    {
        if (empty($this->site_url)) {
            throw new RuntimeException('Site URL not set');
        }

        if (!$this->file_id || !$this->user_id) {
            throw new RuntimeException('File ID or User ID not set');
        }

        return sprintf(
            "%s/%s?file_id=%s&user_id=%s",
            rtrim($this->site_url, '/'),
            "edit.php",
            urlencode($this->file_id),
            urlencode($this->user_id)
        );
    }

    public function generateDirectDownloadLink(): string
    {
        if (empty($this->site_url) || empty($this->upload_folder['folder_name']) || empty($this->file_name)) {
            throw new RuntimeException('Required parameters not set');
        }

        return sprintf(
            "%s/%s/%s",
            rtrim($this->site_url, '/'),
            $this->upload_folder['folder_name'],
            urlencode($this->file_name)
        );
    }

    public function getFileID(): ?string
    {
        return $this->file_id;
    }

    public function getUserID(): ?string
    {
        return $this->user_id;
    }

    public function setSiteUrl(string $site_url): void
    {
        if (!filter_var($site_url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid site URL provided');
        }
        $this->site_url = rtrim($this->util->sanitize($site_url), '/');
    }

    public function hashName(): bool
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        $this->file_name = hash("sha256", $this->file->getFileHash() . uniqid()) .
            "." . $this->file->getExtension();
        $this->is_hashed = true;

        return true;
    }

    public function createUploadFolder(string $folder_name): void
    {
        $sanitized_folder = $this->util->sanitize($folder_name);

        if (!file_exists($sanitized_folder) && !is_dir($sanitized_folder)) {
            if (!@mkdir($sanitized_folder, 0755, true)) {
                throw new RuntimeException('Failed to create upload folder');
            }

            $this->util->secureDirectory($sanitized_folder, true, true);
        }

        $real_path = realpath($sanitized_folder);
        if ($real_path === false) {
            throw new RuntimeException('Failed to resolve upload folder path');
        }

        $this->upload_folder = [
            "folder_name" => $folder_name,
            "folder_path" => $real_path
        ];
    }

    public function getUploadDirFiles(): array
    {
        if (empty($this->upload_folder['folder_path'])) {
            throw new RuntimeException('Upload folder path not set');
        }

        $files = @scandir($this->upload_folder['folder_path']);
        if ($files === false) {
            throw new RuntimeException('Failed to scan upload directory');
        }

        return array_filter($files, function ($file) {
            return !in_array($file, ['.', '..']);
        });
    }

    public function isFile(string $file_name): bool
    {
        $sanitized_file = $this->util->sanitize($file_name);
        return file_exists($sanitized_file) && is_file($sanitized_file);
    }

    public function isDir(string $dir_name): bool
    {
        $sanitized_dir = $this->util->sanitize($dir_name);
        return is_dir($sanitized_dir) && file_exists($sanitized_dir);
    }

    public function addLog(array $message, ?string $id = null): void
    {
        if ($id !== null) {
            $this->logs[$id] = $message;
        } else {
            $this->logs[] = $message;
        }
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function getLog(string $log_id): ?array
    {
        return $this->logs[$log_id] ?? null;
    }

    public function getJSON(): string
    {
        if (!$this->file) {
            throw new RuntimeException('No file has been set');
        }

        $data = [
            "filename" => $this->file_name,
            "filehash" => $this->hash_id,
            "filesize" => $this->util->formatBytes($this->file->getSize()),
            "uploaddate" => date("Y-m-d H:i:s", $this->file->getDate()),
            "filemime" => $this->file->getMime(),
            "user_id" => $this->getUserID(),
            "file_id" => $this->getFileID(),
        ];

        if (!empty($this->site_url)) {
            $data['qrcode'] = $this->generateQrCode();
            $data['downloadlink'] = $this->generateDownloadLink();
            $data['directlink'] = $this->generateDirectDownloadLink();
            $data['deletelink'] = $this->generateDeleteLink();
            $data['editlink'] = $this->generateEditLink();
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function addFile(string $json_string): void
    {
        $file_data = json_decode($json_string, true, 512, JSON_THROW_ON_ERROR);
        $this->files[] = $file_data;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getMessage(int $index): string
    {
        if (!isset(self::ERROR_MESSAGES[$index])) {
            throw new InvalidArgumentException('Invalid message index');
        }
        return self::ERROR_MESSAGES[$index];
    }

    public function generateFileID(): void
    {
        $this->file_id = hash("sha256", uniqid("file-", true));
    }

    public function generateUserID(bool $disable_session = false): bool
    {
        if ($disable_session) {
            $this->user_id = hash("sha256", "user-" . bin2hex(random_bytes(16)));
            return true;
        }

        if (!isset($_SESSION)) {
            if (session_status() === PHP_SESSION_DISABLED) {
                throw new RuntimeException('Sessions are disabled');
            }

            if (session_status() === PHP_SESSION_NONE) {
                if (!session_start()) {
                    throw new RuntimeException('Failed to start session');
                }
            }
        }

        $this->user_id = $_SESSION['user_id'] ?? hash("sha256", "user-" . session_id());

        // Store the user_id in the session for future use
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $this->user_id;
        }

        return true;
    }

    public function injectClass(string $class_name, object $class): void
    {
        if (!property_exists($this, $class_name)) {
            throw new InvalidArgumentException("Invalid class name: {$class_name}");
        }
        $this->$class_name = $class;
    }
}
