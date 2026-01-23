<?php

namespace Farisc0de\PhpFileUploading;

use Farisc0de\PhpFileUploading\Events\EventDispatcher;
use Farisc0de\PhpFileUploading\Events\FileEvent;
use Farisc0de\PhpFileUploading\Events\ValidationEvent;
use Farisc0de\PhpFileUploading\Events\ScanEvent;
use Farisc0de\PhpFileUploading\Events\UploadEvents;
use Farisc0de\PhpFileUploading\Exception\ValidationException;
use Farisc0de\PhpFileUploading\Exception\StorageException;
use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;
use Farisc0de\PhpFileUploading\Logging\LogLevel;
use Farisc0de\PhpFileUploading\RateLimiting\RateLimiterInterface;
use Farisc0de\PhpFileUploading\Security\VirusScannerInterface;
use Farisc0de\PhpFileUploading\Security\NullScanner;
use Farisc0de\PhpFileUploading\Storage\StorageInterface;
use Farisc0de\PhpFileUploading\Storage\LocalStorage;
use Farisc0de\PhpFileUploading\Validation\ValidationChain;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Production-ready upload manager that combines all features
 *
 * @package PhpFileUploading
 */
class UploadManager
{
    use LoggerAwareTrait;

    private StorageInterface $storage;
    private ?ValidationChain $validator = null;
    private ?RateLimiterInterface $rateLimiter = null;
    private VirusScannerInterface $virusScanner;
    private EventDispatcher $eventDispatcher;
    private Utility $utility;

    private bool $hashFilenames = true;
    private string $hashAlgorithm = 'sha256';
    private ?string $siteUrl = null;
    private ?string $userId = null;
    private ?string $fileId = null;
    private ?string $baseFolderName = null;

    public function __construct(
        StorageInterface $storage,
        ?ValidationChain $validator = null,
        ?RateLimiterInterface $rateLimiter = null,
        ?VirusScannerInterface $virusScanner = null,
        ?EventDispatcher $eventDispatcher = null
    ) {
        $this->storage = $storage;
        $this->validator = $validator;
        $this->rateLimiter = $rateLimiter;
        $this->virusScanner = $virusScanner ?? new NullScanner();
        $this->eventDispatcher = $eventDispatcher ?? new EventDispatcher();
        $this->utility = new Utility();
    }

    /**
     * Upload a file with full validation, scanning, and event handling
     *
     * @param File $file The file to upload
     * @param string $destination Destination path (relative to storage root)
     * @param string|null $identifier Rate limit identifier (e.g., IP address)
     * @return UploadResult The upload result
     */
    public function upload(File $file, string $destination = '', ?string $identifier = null): UploadResult
    {
        $result = new UploadResult();
        $result->setOriginalFilename($file->getName());

        try {
            // Rate limiting check
            if ($this->rateLimiter !== null && $identifier !== null) {
                $rateLimitResult = $this->rateLimiter->check($identifier);
                if (!$rateLimitResult->isAllowed()) {
                    $this->dispatch(new FileEvent(UploadEvents::RATE_LIMIT_EXCEEDED, $file, [
                        'identifier' => $identifier,
                        'retry_after' => $rateLimitResult->getRetryAfter(),
                    ]));

                    throw ValidationException::rateLimitExceeded(
                        $identifier,
                        $rateLimitResult->getLimit(),
                        $this->rateLimiter->getConfig()['window']
                    );
                }
                $result->setRateLimitHeaders($rateLimitResult->getHeaders());
            }

            // Dispatch before validation event
            $beforeValidation = new ValidationEvent(UploadEvents::BEFORE_VALIDATION, $file);
            $this->dispatch($beforeValidation);

            if ($beforeValidation->isPropagationStopped()) {
                throw new \RuntimeException('Upload cancelled by event listener');
            }

            // Validation
            if ($this->validator !== null) {
                $validationResult = $this->validator->validate($file);

                $afterValidation = new ValidationEvent(UploadEvents::AFTER_VALIDATION, $file, $validationResult);
                $this->dispatch($afterValidation);

                if ($validationResult->isFailed()) {
                    $this->dispatch(new ValidationEvent(UploadEvents::VALIDATION_FAILED, $file, $validationResult));

                    $result->setValidationResult($validationResult);
                    $result->setSuccess(false);
                    $result->setError($validationResult->getFirstError() ?? 'Validation failed');

                    return $result;
                }

                $result->setValidationResult($validationResult);
            }

            // Virus scanning
            $beforeScan = new ScanEvent(UploadEvents::BEFORE_SCAN, $file);
            $this->dispatch($beforeScan);

            $scanResult = $this->virusScanner->scan($file->getTempName());

            $afterScan = new ScanEvent(UploadEvents::AFTER_SCAN, $file, $scanResult);
            $this->dispatch($afterScan);

            if ($scanResult->isInfected()) {
                $this->dispatch(new ScanEvent(UploadEvents::VIRUS_DETECTED, $file, $scanResult));

                throw ValidationException::virusDetected($file->getName(), $scanResult->getVirusName());
            }

            $result->setScanResult($scanResult);

            // Generate filename
            $filename = $this->generateFilename($file);
            $fullPath = $destination ? rtrim($destination, '/') . '/' . $filename : $filename;

            // Dispatch before upload event
            $beforeUpload = new FileEvent(UploadEvents::BEFORE_UPLOAD, $file, [
                'destination' => $fullPath,
            ]);
            $this->dispatch($beforeUpload);

            if ($beforeUpload->isPropagationStopped()) {
                throw new \RuntimeException('Upload cancelled by event listener');
            }

            // Store the file
            $stream = fopen($file->getTempName(), 'rb');
            if ($stream === false) {
                throw StorageException::readFailed($file->getTempName(), 'Failed to open source file');
            }

            try {
                $this->storage->writeStream($fullPath, $stream);
            } finally {
                fclose($stream);
            }

            // Set result data
            $result->setSuccess(true);
            $result->setStoredFilename($filename);
            $result->setStoredPath($fullPath);
            $result->setFileSize($file->getSize());
            $result->setMimeType($file->getMime());
            $result->setFileHash($file->getFileHash());

            // Try to get public URL
            try {
                $result->setPublicUrl($this->storage->publicUrl($fullPath));
            } catch (\Exception $e) {
                // Public URL not available
            }

            // Set link generation data
            if ($this->siteUrl !== null) {
                $result->setSiteUrl($this->siteUrl);
                
                // Use baseFolderName if set, otherwise combine with destination
                $folderName = $this->baseFolderName ?? '';
                if (!empty($destination)) {
                    $folderName = $folderName ? $folderName . '/' . $destination : $destination;
                }
                $result->setFolderName($folderName);
                $result->setHashId($file->getFileHash());
                
                if ($this->userId !== null) {
                    $result->setUserId($this->userId);
                }
                
                if ($this->fileId !== null) {
                    $result->setFileId($this->fileId);
                }
            }

            // Dispatch after upload event
            $this->dispatch(new FileEvent(UploadEvents::AFTER_UPLOAD, $file, [
                'path' => $fullPath,
                'filename' => $filename,
            ]));

            $this->logInfo('File uploaded successfully: {filename}', [
                'filename' => $filename,
                'original' => $file->getName(),
                'size' => $file->getSize(),
            ]);

        } catch (ValidationException $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());
            $result->setErrorCode($e->getCode());

            $this->logWarning('Upload validation failed: {error}', [
                'error' => $e->getMessage(),
                'filename' => $file->getName(),
            ]);

            $this->dispatch(new FileEvent(UploadEvents::UPLOAD_FAILED, $file, [
                'error' => $e->getMessage(),
            ]));

        } catch (\Exception $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());

            $this->logError('Upload failed: {error}', [
                'error' => $e->getMessage(),
                'filename' => $file->getName(),
            ]);

            $this->dispatch(new FileEvent(UploadEvents::UPLOAD_FAILED, $file, [
                'error' => $e->getMessage(),
            ]));
        }

        return $result;
    }

    /**
     * Upload multiple files
     *
     * @param array $files Array of File objects
     * @param string $destination Destination path
     * @param string|null $identifier Rate limit identifier
     * @return UploadResult[] Array of upload results
     */
    public function uploadMultiple(array $files, string $destination = '', ?string $identifier = null): array
    {
        $results = [];

        foreach ($files as $file) {
            if ($file instanceof File) {
                $results[] = $this->upload($file, $destination, $identifier);
            }
        }

        return $results;
    }

    /**
     * Generate a filename for the uploaded file
     */
    private function generateFilename(File $file): string
    {
        if ($this->hashFilenames) {
            $hash = hash($this->hashAlgorithm, $file->getFileHash() . uniqid('', true));
            return $hash . '.' . $file->getExtension();
        }

        return $file->getName();
    }

    /**
     * Dispatch an event
     */
    private function dispatch($event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    /**
     * Set whether to hash filenames
     */
    public function setHashFilenames(bool $hash): self
    {
        $this->hashFilenames = $hash;
        return $this;
    }

    /**
     * Set the hash algorithm for filenames
     */
    public function setHashAlgorithm(string $algorithm): self
    {
        $this->hashAlgorithm = $algorithm;
        return $this;
    }

    /**
     * Set the validator
     */
    public function setValidator(ValidationChain $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Set the rate limiter
     */
    public function setRateLimiter(RateLimiterInterface $rateLimiter): self
    {
        $this->rateLimiter = $rateLimiter;
        return $this;
    }

    /**
     * Set the virus scanner
     */
    public function setVirusScanner(VirusScannerInterface $scanner): self
    {
        $this->virusScanner = $scanner;
        return $this;
    }

    /**
     * Get the event dispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }

    /**
     * Get the storage adapter
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Set site URL for link generation
     */
    public function setSiteUrl(?string $url): self
    {
        $this->siteUrl = $url ? rtrim($url, '/') : null;
        return $this;
    }

    /**
     * Get site URL
     */
    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    /**
     * Set user ID for link generation
     */
    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get user ID
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Set file ID for link generation (from your database)
     */
    public function setFileId(?string $fileId): self
    {
        $this->fileId = $fileId;
        return $this;
    }

    /**
     * Get file ID
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    /**
     * Set base folder name for direct link generation
     * 
     * Use this when your storage root is already a subfolder (e.g., uploads/user123)
     * and you need the direct link to include that path.
     * 
     * @param string|null $folderName The base folder path (e.g., 'uploads/user123')
     */
    public function setBaseFolderName(?string $folderName): self
    {
        $this->baseFolderName = $folderName ? trim($folderName, '/') : null;
        return $this;
    }

    /**
     * Get base folder name
     */
    public function getBaseFolderName(): ?string
    {
        return $this->baseFolderName;
    }

    /**
     * Generate a secure file ID (static version)
     * 
     * Can be called without an UploadManager instance.
     * Uses cryptographically secure random bytes combined with high-resolution
     * timestamp for uniqueness and unpredictability.
     *
     * @param string $algorithm Hash algorithm to use (default: sha256)
     * @return string The generated file ID
     */
    public static function createFileId(string $algorithm = 'sha256'): string
    {
        $entropy = sprintf(
            'file-%s-%s-%s',
            bin2hex(random_bytes(16)),
            hrtime(true),
            uniqid('', true)
        );
        
        return hash($algorithm, $entropy);
    }

    /**
     * Generate a secure file ID and set it on this instance
     *
     * @param string $algorithm Hash algorithm to use (default: sha256)
     * @return string The generated file ID
     */
    public function generateFileId(string $algorithm = 'sha256'): string
    {
        $this->fileId = self::createFileId($algorithm);
        
        $this->logDebug('Generated file ID: {file_id}', ['file_id' => $this->fileId]);
        
        return $this->fileId;
    }

    /**
     * Generate a secure user ID (static version)
     * 
     * Can be called without an UploadManager instance.
     * Supports session-based or stateless generation.
     *
     * @param bool $stateless If true, generates ID without session (for APIs/CLI)
     * @return string The generated or retrieved user ID
     * @throws \Farisc0de\PhpFileUploading\Exception\ConfigurationException If sessions are required but unavailable
     */
    public static function createUserId(bool $stateless = false): string
    {
        // Stateless mode - no session dependency (for APIs, CLI, microservices)
        if ($stateless) {
            $entropy = sprintf(
                'user-%s-%s-%s',
                bin2hex(random_bytes(16)),
                hrtime(true),
                getmypid()
            );
            
            return hash('sha256', $entropy);
        }

        // Session-based mode
        if (session_status() === PHP_SESSION_DISABLED) {
            throw \Farisc0de\PhpFileUploading\Exception\ConfigurationException::missingDependency(
                'sessions',
                'PHP sessions must be enabled for session-based user ID generation. Use stateless mode for APIs.'
            );
        }

        if (session_status() === PHP_SESSION_NONE) {
            if (!@session_start()) {
                throw \Farisc0de\PhpFileUploading\Exception\ConfigurationException::missingDependency(
                    'sessions',
                    'Failed to start PHP session. Check session configuration or use stateless mode.'
                );
            }
        }

        // Check for existing session user ID
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        // Generate new session-based user ID
        $entropy = sprintf(
            'user-%s-%s-%s',
            session_id(),
            bin2hex(random_bytes(8)),
            hrtime(true)
        );
        
        $userId = hash('sha256', $entropy);
        $_SESSION['user_id'] = $userId;
        
        return $userId;
    }

    /**
     * Generate a secure user ID and set it on this instance
     * 
     * Can operate in two modes:
     * - Session-based: Uses PHP session for persistent user identification
     * - Stateless: Generates a random ID without session dependency
     *
     * @param bool $stateless If true, generates ID without session (for APIs/CLI)
     * @return string The generated or retrieved user ID
     * @throws \Farisc0de\PhpFileUploading\Exception\ConfigurationException If sessions are required but unavailable
     */
    public function generateUserId(bool $stateless = false): string
    {
        $this->userId = self::createUserId($stateless);
        
        $this->logDebug('User ID set: {user_id}', ['user_id' => $this->userId]);
        
        return $this->userId;
    }

    /**
     * Delete a file
     */
    public function delete(string $path): bool
    {
        try {
            $this->dispatch(new FileEvent(UploadEvents::BEFORE_DELETE, null, ['path' => $path]));

            $this->storage->delete($path);

            $this->dispatch(new FileEvent(UploadEvents::AFTER_DELETE, null, ['path' => $path]));

            $this->logInfo('File deleted: {path}', ['path' => $path]);

            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to delete file: {error}', [
                'error' => $e->getMessage(),
                'path' => $path,
            ]);

            return false;
        }
    }
}
