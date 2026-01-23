<?php

namespace Farisc0de\PhpFileUploading\Validation;

use Farisc0de\PhpFileUploading\File;

/**
 * Interface for file validators
 *
 * @package PhpFileUploading
 */
interface ValidatorInterface
{
    /**
     * Validate a file
     *
     * @param File $file The file to validate
     * @return ValidationResult The validation result
     */
    public function validate(File $file): ValidationResult;

    /**
     * Get the validator name
     */
    public function getName(): string;
}
