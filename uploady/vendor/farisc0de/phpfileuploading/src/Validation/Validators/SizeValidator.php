<?php

namespace Farisc0de\PhpFileUploading\Validation\Validators;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\Validation\ValidatorInterface;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Validates file size
 *
 * @package PhpFileUploading
 */
class SizeValidator implements ValidatorInterface
{
    private ?int $minSize;
    private ?int $maxSize;
    private array $categoryLimits;
    private Utility $utility;

    public function __construct(
        ?string $minSize = null,
        ?string $maxSize = null,
        array $categoryLimits = []
    ) {
        $this->utility = new Utility();
        $this->minSize = $minSize ? (int)$this->utility->sizeInBytes($minSize) : null;
        $this->maxSize = $maxSize ? (int)$this->utility->sizeInBytes($maxSize) : null;
        $this->categoryLimits = $categoryLimits;
    }

    public function validate(File $file): ValidationResult
    {
        try {
            $size = $file->getSize();
        } catch (\Exception $e) {
            return ValidationResult::failure(
                'Failed to determine file size',
                'SIZE_DETECTION_FAILED',
                ['error' => $e->getMessage()]
            );
        }

        // Determine the applicable max size (category-specific or default)
        $maxSize = $this->getApplicableMaxSize($file);

        // Check minimum size
        if ($this->minSize !== null && $size < $this->minSize) {
            return ValidationResult::failure(
                "File size ({$this->formatSize($size)}) is below minimum ({$this->formatSize($this->minSize)})",
                'FILE_TOO_SMALL',
                ['size' => $size, 'min_size' => $this->minSize]
            );
        }

        // Check maximum size
        if ($maxSize !== null && $size > $maxSize) {
            return ValidationResult::failure(
                "File size ({$this->formatSize($size)}) exceeds maximum ({$this->formatSize($maxSize)})",
                'FILE_TOO_LARGE',
                ['size' => $size, 'max_size' => $maxSize]
            );
        }

        return ValidationResult::success([
            'size' => $size,
            'formatted_size' => $this->formatSize($size)
        ]);
    }

    public function getName(): string
    {
        return 'size';
    }

    /**
     * Get the applicable max size based on file category
     */
    private function getApplicableMaxSize(File $file): ?int
    {
        if (empty($this->categoryLimits)) {
            return $this->maxSize;
        }

        try {
            $mime = $file->getMime();
            $category = $this->getCategoryFromMime($mime);

            if ($category && isset($this->categoryLimits[$category])) {
                return (int)$this->utility->sizeInBytes($this->categoryLimits[$category]);
            }

            if (isset($this->categoryLimits['default'])) {
                return (int)$this->utility->sizeInBytes($this->categoryLimits['default']);
            }
        } catch (\Exception $e) {
            // Fall back to default max size
        }

        return $this->maxSize;
    }

    /**
     * Determine file category from MIME type
     */
    private function getCategoryFromMime(string $mime): ?string
    {
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }
        if (str_starts_with($mime, 'application/pdf') ||
            str_starts_with($mime, 'application/msword') ||
            str_starts_with($mime, 'application/vnd.openxmlformats-officedocument') ||
            str_starts_with($mime, 'text/')) {
            return 'document';
        }
        if (str_starts_with($mime, 'application/zip') ||
            str_starts_with($mime, 'application/x-rar') ||
            str_starts_with($mime, 'application/x-7z') ||
            str_starts_with($mime, 'application/x-tar') ||
            str_starts_with($mime, 'application/gzip')) {
            return 'archive';
        }

        return null;
    }

    /**
     * Format size for display
     */
    private function formatSize(int $bytes): string
    {
        return $this->utility->formatBytes($bytes);
    }

    /**
     * Set minimum size
     */
    public function setMinSize(?string $size): self
    {
        $this->minSize = $size ? (int)$this->utility->sizeInBytes($size) : null;
        return $this;
    }

    /**
     * Set maximum size
     */
    public function setMaxSize(?string $size): self
    {
        $this->maxSize = $size ? (int)$this->utility->sizeInBytes($size) : null;
        return $this;
    }

    /**
     * Set category-specific size limits
     */
    public function setCategoryLimits(array $limits): self
    {
        $this->categoryLimits = $limits;
        return $this;
    }
}
