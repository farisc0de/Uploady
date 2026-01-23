<?php

namespace Farisc0de\PhpFileUploading\Validation\Validators;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Validation\ValidatorInterface;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Validates file extensions against an allowed list
 *
 * @package PhpFileUploading
 */
class ExtensionValidator implements ValidatorInterface
{
    private array $allowedExtensions;
    private array $blockedExtensions;

    public function __construct(array $allowedExtensions = [], array $blockedExtensions = [])
    {
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
        $this->blockedExtensions = array_map('strtolower', $blockedExtensions);
    }

    public function validate(File $file): ValidationResult
    {
        $extension = strtolower($file->getExtension());

        // Check blocked extensions first
        if (!empty($this->blockedExtensions) && in_array($extension, $this->blockedExtensions, true)) {
            return ValidationResult::failure(
                "File extension '{$extension}' is blocked",
                'BLOCKED_EXTENSION',
                ['extension' => $extension, 'blocked' => $this->blockedExtensions]
            );
        }

        // Check allowed extensions
        if (!empty($this->allowedExtensions) && !in_array($extension, $this->allowedExtensions, true)) {
            return ValidationResult::failure(
                "File extension '{$extension}' is not allowed",
                'INVALID_EXTENSION',
                ['extension' => $extension, 'allowed' => $this->allowedExtensions]
            );
        }

        return ValidationResult::success(['extension' => $extension]);
    }

    public function getName(): string
    {
        return 'extension';
    }

    /**
     * Set allowed extensions
     */
    public function setAllowedExtensions(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set blocked extensions
     */
    public function setBlockedExtensions(array $extensions): self
    {
        $this->blockedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Add an allowed extension
     */
    public function addAllowedExtension(string $extension): self
    {
        $this->allowedExtensions[] = strtolower($extension);
        return $this;
    }

    /**
     * Add a blocked extension
     */
    public function addBlockedExtension(string $extension): self
    {
        $this->blockedExtensions[] = strtolower($extension);
        return $this;
    }
}
