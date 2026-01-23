<?php

namespace Farisc0de\PhpFileUploading;

use Farisc0de\PhpFileUploading\Exception\UploadException;
use Farisc0de\PhpFileUploading\Exception\ValidationException;
use Farisc0de\PhpFileUploading\Exception\StorageException;
use Farisc0de\PhpFileUploading\Exception\ConfigurationException;
use Farisc0de\PhpFileUploading\Exception\ImageException;
use Farisc0de\PhpFileUploading\Logging\LoggerInterface;
use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;
use Farisc0de\PhpFileUploading\Logging\NullLogger;
use Farisc0de\PhpFileUploading\Events\EventDispatcher;
use Farisc0de\PhpFileUploading\Events\FileEvent;
use Farisc0de\PhpFileUploading\Events\ValidationEvent;
use Farisc0de\PhpFileUploading\Events\UploadEvents;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;
use RuntimeException;
use InvalidArgumentException;
use Exception;

/**
 * PHP Library to help you build your own file sharing website.
 * Supports enhanced file filtering with categorized MIME types and size limits.
 *
 * @version 2.6.0
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */

final class Upload
{
    use LoggerAwareTrait;

    private ?File $file;
    private Utility $util;
    private array $name_array = [];
    private array $filter_array = [];
    private array $upload_folder = [];
    private int $size;
    private array $size_limits = [];
    private array $categories = [];
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
    private ?EventDispatcher $eventDispatcher = null;
    private bool $throwExceptions = false;

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

        // Initialize with NullLogger by default
        $this->logger = new NullLogger();
    }

    private function validateConstructorParams(array $upload_folder, string $site_url, string $size): void
    {
        if (!empty($upload_folder) && (!isset($upload_folder['folder_name']) || !isset($upload_folder['folder_path']))) {
            throw ConfigurationException::invalidConfig('upload_folder', 'incomplete array', 'array with folder_name and folder_path keys');
        }

        if (!empty($site_url) && !filter_var($site_url, FILTER_VALIDATE_URL)) {
            throw ConfigurationException::invalidConfig('site_url', $site_url, 'a valid URL');
        }

        if (!preg_match('/^\d+\s*(?:B|KB|MB|GB|TB)$/i', $size)) {
            throw ConfigurationException::invalidConfig('size', $size, 'format like "5 MB" or "1 GB"');
        }
    }

    /**
     * Set the event dispatcher for upload events
     */
    public function setEventDispatcher(EventDispatcher $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * Get the event dispatcher
     */
    public function getEventDispatcher(): ?EventDispatcher
    {
        return $this->eventDispatcher;
    }

    /**
     * Enable throwing exceptions instead of returning false
     */
    public function enableExceptions(bool $enable = true): void
    {
        $this->throwExceptions = $enable;
    }

    /**
     * Dispatch an event if dispatcher is set
     */
    private function dispatchEvent($event): void
    {
        if ($this->eventDispatcher !== null) {
            $this->eventDispatcher->dispatch($event);
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
            $this->logError('Filter configuration file not found: {path}', ['path' => $filterPath]);
            throw ConfigurationException::missingConfig('filter.json');
        }

        $filterContent = file_get_contents($filterPath);
        if ($filterContent === false) {
            $this->logError('Unable to read filter configuration');
            throw ConfigurationException::missingConfig('filter.json (unreadable)');
        }

        $filters = json_decode($filterContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError('Invalid filter configuration format: {error}', ['error' => json_last_error_msg()]);
            throw ConfigurationException::invalidConfig('filter.json', 'invalid JSON', 'valid JSON format');
        }

        $this->logDebug('Protection enabled with filter configuration');

        // Load basic filter arrays
        $this->name_array = $filters['forbidden'] ?? [];
        $this->filter_array = $filters['extensions'] ?? [];

        // Load new structure elements if available
        if (isset($filters['size_limits']) && is_array($filters['size_limits'])) {
            $this->size_limits = $filters['size_limits'];
        }

        if (isset($filters['categories']) && is_array($filters['categories'])) {
            $this->categories = $filters['categories'];
        }
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
            throw ConfigurationException::missingConfig('file');
        }

        // Get file category based on MIME type
        $category = $this->getFileCategory();
        $sizeLimit = $this->size; // Default size limit

        // Use category-specific size limit if available
        if (!empty($this->size_limits) && isset($this->size_limits[$category])) {
            $sizeLimit = $this->util->sizeInBytes($this->size_limits[$category]);
        } elseif (!empty($this->size_limits) && isset($this->size_limits['default'])) {
            $sizeLimit = $this->util->sizeInBytes($this->size_limits['default']);
        }

        if ($this->file->getSize() > $sizeLimit) {
            $this->addLog(['filename' => $this->file_name, "message" => 4]);
            $this->logWarning('File size exceeds limit: {size} > {limit}', [
                'size' => $this->file->getSize(),
                'limit' => $sizeLimit,
                'filename' => $this->file_name
            ]);

            if ($this->throwExceptions) {
                throw ValidationException::fileTooLarge($this->file->getSize(), $sizeLimit);
            }
            return false;
        }

        return true;
    }

    /**
     * Determine the category of the current file based on its MIME type
     *
     * @return string The category name ('images', 'documents', 'audio', 'video', 'archives', or 'other')
     */
    private function getFileCategory(): string
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        $mime = $this->file->getMime();

        // If categories are defined in filter.json
        if (!empty($this->categories)) {
            foreach ($this->categories as $category => $mimeTypes) {
                if (in_array($mime, $mimeTypes, true)) {
                    return $category;
                }
            }
        }

        // Fallback category detection based on MIME type prefix
        if (strpos($mime, 'image/') === 0) {
            return 'image';
        } elseif (strpos($mime, 'audio/') === 0) {
            return 'audio';
        } elseif (strpos($mime, 'video/') === 0) {
            return 'video';
        } elseif (
            strpos($mime, 'application/pdf') === 0 ||
            strpos($mime, 'application/msword') === 0 ||
            strpos($mime, 'application/vnd.openxmlformats-officedocument') === 0 ||
            strpos($mime, 'text/') === 0
        ) {
            return 'document';
        } elseif (
            strpos($mime, 'application/zip') === 0 ||
            strpos($mime, 'application/x-rar') === 0 ||
            strpos($mime, 'application/x-7z') === 0 ||
            strpos($mime, 'application/x-tar') === 0 ||
            strpos($mime, 'application/gzip') === 0
        ) {
            return 'archive';
        }

        return 'other';
    }

    public function checkDimension(int $operation = 2): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        if (!$this->isImage()) {
            throw ValidationException::notAnImage($this->file->getMime());
        }

        $image_data = @getimagesize($this->file->getTempName());
        if ($image_data === false) {
            throw ImageException::processingFailed($this->file_name, 'Unable to get image dimensions');
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
                throw ConfigurationException::invalidConfig('operation', (string)$operation, '0-5');
        }

        return true;
    }

    public function isImage(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        // First check using the categories if available
        if (!empty($this->categories) && isset($this->categories['images'])) {
            if (in_array($this->file->getMime(), $this->categories['images'], true)) {
                return true;
            }
        } else if (in_array($this->file->getMime(), self::ALLOWED_IMAGE_MIMES, true)) {
            return true;
        }

        $this->addLog(['filename' => $this->file_name, "message" => 13]);
        $this->logDebug('File is not an image: {mime}', ['mime' => $this->file->getMime()]);
        return false;
    }

    public function upload(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        // Dispatch before upload event
        $this->dispatchEvent(new FileEvent(UploadEvents::BEFORE_UPLOAD, $this->file, [
            'filename' => $this->file_name ?? $this->file->getName(),
        ]));

        if (!$this->checkIfNotEmpty()) {
            $this->dispatchEvent(new FileEvent(UploadEvents::UPLOAD_FAILED, $this->file, [
                'error' => 'Empty file',
            ]));
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

                $this->logInfo('File uploaded successfully: {filename}', [
                    'filename' => $this->file_name,
                    'size' => $this->file->getSize(),
                ]);

                // Dispatch after upload event
                $this->dispatchEvent(new FileEvent(UploadEvents::AFTER_UPLOAD, $this->file, [
                    'filename' => $this->file_name,
                    'path' => $this->upload_folder['folder_path'] . DIRECTORY_SEPARATOR . $this->file_name,
                ]));

                return true;
            }

            $this->dispatchEvent(new FileEvent(UploadEvents::UPLOAD_FAILED, $this->file, [
                'error' => 'Failed to move file',
            ]));
            return false;
        } catch (Exception $e) {
            $this->addLog(['filename' => $this->file_name ?? 'unknown', "message" => $e->getMessage()]);
            $this->logError('Upload failed: {error}', ['error' => $e->getMessage()]);

            $this->dispatchEvent(new FileEvent(UploadEvents::UPLOAD_FAILED, $this->file, [
                'error' => $e->getMessage(),
            ]));

            if ($this->throwExceptions) {
                throw $e;
            }
            return false;
        }
    }

    private function validateUpload(): void
    {
        if (empty($this->upload_folder)) {
            throw ConfigurationException::missingConfig('upload_folder');
        }

        if (!is_dir($this->upload_folder['folder_path'])) {
            throw StorageException::directoryCreateFailed($this->upload_folder['folder_path'], 'Directory does not exist');
        }

        if (!is_writable($this->upload_folder['folder_path'])) {
            throw StorageException::writeFailed($this->upload_folder['folder_path'], 'Directory is not writable');
        }
    }

    public function moveFile(string $filename): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        $this->disableTimeLimit();

        try {
            $targetPath = $this->upload_folder['folder_path'] . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($targetPath)) {
                $this->addLog(['filename' => $filename, "message" => 6]);
                $this->logWarning('File already exists: {path}', ['path' => $targetPath]);

                if ($this->throwExceptions) {
                    throw StorageException::writeFailed($targetPath, 'File already exists');
                }
                return false;
            }

            return $this->moveFileInChunks($targetPath);
        } catch (Exception $e) {
            $this->addLog(['filename' => $filename, "message" => $e->getMessage()]);
            $this->logError('Failed to move file: {error}', ['error' => $e->getMessage()]);

            if ($this->throwExceptions && !($e instanceof UploadException)) {
                throw StorageException::moveFailed($this->file->getTempName(), $targetPath, $e->getMessage());
            }
            return false;
        }
    }

    private function moveFileInChunks(string $targetPath): bool
    {
        $chunk_size = 4096;
        $handle = @fopen($this->file->getTempName(), "rb");
        $fp = @fopen($targetPath, 'wb');

        if (!$handle || !$fp) {
            throw StorageException::writeFailed($targetPath, 'Failed to open file streams');
        }

        try {
            while (!feof($handle)) {
                $contents = fread($handle, $chunk_size);
                if ($contents === false) {
                    throw StorageException::readFailed($this->file->getTempName(), 'Failed to read from source file');
                }
                if (fwrite($fp, $contents) === false) {
                    throw StorageException::writeFailed($targetPath, 'Failed to write to target file');
                }
            }
        } finally {
            fclose($handle);
            if (is_resource($fp)) {
                if (!fclose($fp)) {
                    throw StorageException::writeFailed($targetPath, 'Failed to close target file');
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
            throw ConfigurationException::missingConfig('user_id');
        }

        $upload_folder = $main_upload_folder ?? $this->upload_folder['folder_path'] ?? null;

        if ($upload_folder === null) {
            throw ConfigurationException::missingConfig('upload_folder');
        }

        $user_cloud = $upload_folder . DIRECTORY_SEPARATOR . $user_id;

        if (!file_exists($user_cloud)) {
            if (!@mkdir($user_cloud, 0755, true)) {
                throw StorageException::directoryCreateFailed($user_cloud);
            }
            $this->logInfo('Created user cloud directory: {path}', ['path' => $user_cloud]);
        }

        return true;
    }

    public function getUserCloud(?string $main_upload_folder = null): string
    {
        $user_id = $this->getUserID();
        if ($user_id === null) {
            throw ConfigurationException::missingConfig('user_id');
        }

        $upload_folder = $main_upload_folder ?? $this->upload_folder['folder_path'] ?? null;

        if ($upload_folder === null) {
            throw ConfigurationException::missingConfig('upload_folder');
        }

        return $upload_folder . DIRECTORY_SEPARATOR . $user_id;
    }

    public function checkExtension(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        if (empty($this->filter_array)) {
            throw ConfigurationException::missingConfig('filter_array (call enableProtection() first)');
        }

        // Dispatch before validation event
        $this->dispatchEvent(new FileEvent(UploadEvents::BEFORE_VALIDATION, $this->file, [
            'type' => 'extension',
        ]));

        if (!isset($this->filter_array[$this->file->getExtension()])) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);
            $this->logWarning('Invalid extension: {ext}', [
                'ext' => $this->file->getExtension(),
                'filename' => $this->file_name
            ]);

            $this->dispatchEvent(new FileEvent(UploadEvents::VALIDATION_FAILED, $this->file, [
                'type' => 'extension',
                'extension' => $this->file->getExtension(),
            ]));

            if ($this->throwExceptions) {
                throw ValidationException::invalidExtension(
                    $this->file->getExtension(),
                    array_keys($this->filter_array)
                );
            }
            return false;
        }

        return true;
    }

    public function checkMime(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        if (empty($this->filter_array)) {
            throw ConfigurationException::missingConfig('filter_array (call enableProtection() first)');
        }

        $mime = mime_content_type($this->file->getTempName());
        if ($mime === false) {
            $this->addLog(['filename' => $this->file_name, "message" => 2]);
            $this->logWarning('Failed to detect MIME type for: {filename}', ['filename' => $this->file_name]);

            if ($this->throwExceptions) {
                throw ValidationException::invalidMime('unknown', 'detectable MIME type');
            }
            return false;
        }

        $extension = $this->file->getExtension();
        $expectedMime = $this->filter_array[$extension] ?? null;

        // If the extension doesn't exist in our filter array
        if ($expectedMime === null) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);
            $this->logWarning('Extension not in filter array: {ext}', ['ext' => $extension]);

            if ($this->throwExceptions) {
                throw ValidationException::invalidExtension($extension, array_keys($this->filter_array));
            }
            return false;
        }

        // Check if the MIME type matches what we expect for this extension
        if ($expectedMime !== $mime || $mime !== $this->file->getMime()) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);
            $this->logWarning('MIME type mismatch: {actual} vs {expected}', [
                'actual' => $mime,
                'expected' => $expectedMime,
                'filename' => $this->file_name
            ]);

            if ($this->throwExceptions) {
                throw ValidationException::invalidMime($mime, $expectedMime);
            }
            return false;
        }

        return true;
    }

    public function checkForbidden(): bool
    {
        if (empty($this->file_name)) {
            throw ConfigurationException::missingConfig('file_name');
        }

        if (empty($this->name_array)) {
            return true; // No forbidden names set, so all names are allowed
        }

        if (in_array($this->file_name, $this->name_array, true)) {
            $this->addLog(['filename' => $this->file_name, "message" => 3]);
            $this->logWarning('Forbidden filename detected: {filename}', ['filename' => $this->file_name]);

            if ($this->throwExceptions) {
                throw ValidationException::forbiddenName($this->file_name);
            }
            return false;
        }

        return true;
    }

    public function checkIfNotEmpty(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
        }

        if ($this->file->isEmpty()) {
            $this->addLog(['filename' => $this->file_name, "message" => 5]);
            $this->logWarning('Empty file uploaded: {filename}', ['filename' => $this->file_name]);

            if ($this->throwExceptions) {
                throw ValidationException::emptyFile();
            }
            return false;
        }

        return true;
    }

    public function generateQrCode(): string
    {
        if (empty($this->site_url)) {
            throw ConfigurationException::missingConfig('site_url');
        }

        return sprintf(
            "https://quickchart.io/qr?text=%s&size=150",
            urlencode($this->generateDownloadLink())
        );
    }

    public function generateDownloadLink(): string
    {
        if (empty($this->site_url)) {
            throw ConfigurationException::missingConfig('site_url');
        }

        if (!$this->file_id) {
            throw ConfigurationException::missingConfig('file_id');
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
            throw ConfigurationException::missingConfig('site_url');
        }

        if (!$this->file_id || !$this->user_id) {
            throw ConfigurationException::missingConfig('file_id or user_id');
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
            throw ConfigurationException::missingConfig('site_url');
        }

        if (!$this->file_id || !$this->user_id) {
            throw ConfigurationException::missingConfig('file_id or user_id');
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
            throw ConfigurationException::missingConfig('site_url, upload_folder, or file_name');
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
            throw ConfigurationException::invalidConfig('site_url', $site_url, 'a valid URL');
        }
        $this->site_url = rtrim($this->util->sanitize($site_url), '/');
    }

    public function hashName(): bool
    {
        if (!$this->file) {
            throw ConfigurationException::missingConfig('file');
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
                throw StorageException::directoryCreateFailed($sanitized_folder);
            }

            $this->util->secureDirectory($sanitized_folder, true, true);
        }

        $real_path = realpath($sanitized_folder);
        if ($real_path === false) {
            throw StorageException::directoryCreateFailed($sanitized_folder, 'Failed to resolve path');
        }

        $this->logInfo('Created upload folder: {path}', ['path' => $real_path]);

        $this->upload_folder = [
            "folder_name" => $folder_name,
            "folder_path" => $real_path
        ];
    }

    public function getUploadDirFiles(): array
    {
        if (empty($this->upload_folder['folder_path'])) {
            throw ConfigurationException::missingConfig('upload_folder');
        }

        $files = @scandir($this->upload_folder['folder_path']);
        if ($files === false) {
            throw StorageException::readFailed($this->upload_folder['folder_path'], 'Failed to scan directory');
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
            throw ConfigurationException::invalidConfig('message_index', (string)$index, '0-14');
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
                throw ConfigurationException::missingDependency('sessions', 'PHP sessions must be enabled');
            }

            if (session_status() === PHP_SESSION_NONE) {
                if (!session_start()) {
                    throw ConfigurationException::missingDependency('sessions', 'Failed to start session');
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
            throw ConfigurationException::invalidConfig('class_name', $class_name, 'a valid property name');
        }
        $this->$class_name = $class;
    }

    /**
     * Validate file using the new validation system and return a ValidationResult
     *
     * @return ValidationResult
     */
    public function validate(): ValidationResult
    {
        $result = ValidationResult::success();

        if (!$this->file) {
            return ValidationResult::failure('No file has been set', 'NO_FILE');
        }

        // Check if empty
        if ($this->file->isEmpty()) {
            $result->addError('File is empty', 'EMPTY_FILE');
        }

        // Check forbidden names
        if (!empty($this->name_array) && in_array($this->file_name, $this->name_array, true)) {
            $result->addError('Filename is forbidden', 'FORBIDDEN_NAME', ['filename' => $this->file_name]);
        }

        // Check extension
        if (!empty($this->filter_array) && !isset($this->filter_array[$this->file->getExtension()])) {
            $result->addError('Invalid file extension', 'INVALID_EXTENSION', [
                'extension' => $this->file->getExtension(),
                'allowed' => array_keys($this->filter_array)
            ]);
        }

        // Check MIME type
        if (!empty($this->filter_array)) {
            $mime = $this->file->getMime();
            $extension = $this->file->getExtension();
            $expectedMime = $this->filter_array[$extension] ?? null;

            if ($expectedMime !== null && $expectedMime !== $mime) {
                $result->addError('MIME type mismatch', 'MIME_MISMATCH', [
                    'actual' => $mime,
                    'expected' => $expectedMime
                ]);
            }
        }

        // Check size
        $category = $this->getFileCategory();
        $sizeLimit = $this->size;

        if (!empty($this->size_limits) && isset($this->size_limits[$category])) {
            $sizeLimit = $this->util->sizeInBytes($this->size_limits[$category]);
        } elseif (!empty($this->size_limits) && isset($this->size_limits['default'])) {
            $sizeLimit = $this->util->sizeInBytes($this->size_limits['default']);
        }

        if ($this->file->getSize() > $sizeLimit) {
            $result->addError('File size exceeds limit', 'FILE_TOO_LARGE', [
                'size' => $this->file->getSize(),
                'limit' => $sizeLimit
            ]);
        }

        // Add metadata
        $result->setMetadata([
            'filename' => $this->file_name,
            'extension' => $this->file->getExtension(),
            'mime' => $this->file->getMime(),
            'size' => $this->file->getSize(),
            'category' => $category,
        ]);

        // Dispatch validation event
        $this->dispatchEvent(new ValidationEvent(
            $result->isValid() ? UploadEvents::AFTER_VALIDATION : UploadEvents::VALIDATION_FAILED,
            $this->file,
            $result
        ));

        return $result;
    }

    /**
     * Get the current file
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Get the current filename
     */
    public function getFileName(): string
    {
        return $this->file_name ?? '';
    }
}
