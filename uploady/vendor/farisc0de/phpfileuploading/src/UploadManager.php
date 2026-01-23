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
