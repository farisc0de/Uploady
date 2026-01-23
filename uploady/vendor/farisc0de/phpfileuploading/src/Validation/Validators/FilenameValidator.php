<?php

namespace Farisc0de\PhpFileUploading\Validation\Validators;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Validation\ValidatorInterface;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Validates filenames against forbidden names and patterns
 *
 * @package PhpFileUploading
 */
class FilenameValidator implements ValidatorInterface
{
    private array $forbiddenNames;
    private array $forbiddenPatterns;
    private int $maxLength;
    private bool $allowUnicode;

    public function __construct(
        array $forbiddenNames = [],
        array $forbiddenPatterns = [],
        int $maxLength = 255,
        bool $allowUnicode = true
    ) {
        $this->forbiddenNames = array_map('strtolower', $forbiddenNames);
        $this->forbiddenPatterns = $forbiddenPatterns;
        $this->maxLength = $maxLength;
        $this->allowUnicode = $allowUnicode;
    }

    public function validate(File $file): ValidationResult
    {
        $filename = $file->getName();
        $result = ValidationResult::success(['filename' => $filename]);

        // Check for empty filename
        if (empty($filename)) {
            return ValidationResult::failure(
                'Filename cannot be empty',
                'EMPTY_FILENAME',
                []
            );
        }

        // Check filename length
        if (strlen($filename) > $this->maxLength) {
            $result->addError(
                "Filename exceeds maximum length of {$this->maxLength} characters",
                'FILENAME_TOO_LONG',
                ['length' => strlen($filename), 'max_length' => $this->maxLength]
            );
        }

        // Check forbidden names
        if (in_array(strtolower($filename), $this->forbiddenNames, true)) {
            $result->addError(
                "Filename '{$filename}' is forbidden",
                'FORBIDDEN_FILENAME',
                ['filename' => $filename]
            );
        }

        // Check forbidden patterns
        foreach ($this->forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                $result->addError(
                    "Filename matches forbidden pattern",
                    'FORBIDDEN_PATTERN',
                    ['filename' => $filename, 'pattern' => $pattern]
                );
                break;
            }
        }

        // Check for null bytes (security issue)
        if (strpos($filename, "\0") !== false) {
            $result->addError(
                'Filename contains null bytes',
                'NULL_BYTE_DETECTED',
                []
            );
        }

        // Check for path traversal attempts
        if (preg_match('/\.\.[\\/]/', $filename) || preg_match('/^[\\/]/', $filename)) {
            $result->addError(
                'Filename contains path traversal characters',
                'PATH_TRAVERSAL_DETECTED',
                ['filename' => $filename]
            );
        }

        // Check for invalid characters (if unicode not allowed)
        if (!$this->allowUnicode && preg_match('/[^\x20-\x7E]/', $filename)) {
            $result->addError(
                'Filename contains non-ASCII characters',
                'INVALID_CHARACTERS',
                ['filename' => $filename]
            );
        }

        // Check for dangerous Windows filenames
        $dangerousNames = ['CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4',
            'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2', 'LPT3',
            'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'];

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        if (in_array(strtoupper($baseName), $dangerousNames, true)) {
            $result->addWarning(
                "Filename '{$baseName}' is a reserved Windows name",
                'RESERVED_WINDOWS_NAME',
                ['filename' => $filename]
            );
        }

        return $result;
    }

    public function getName(): string
    {
        return 'filename';
    }

    /**
     * Set forbidden filenames
     */
    public function setForbiddenNames(array $names): self
    {
        $this->forbiddenNames = array_map('strtolower', $names);
        return $this;
    }

    /**
     * Add a forbidden filename
     */
    public function addForbiddenName(string $name): self
    {
        $this->forbiddenNames[] = strtolower($name);
        return $this;
    }

    /**
     * Set forbidden patterns (regex)
     */
    public function setForbiddenPatterns(array $patterns): self
    {
        $this->forbiddenPatterns = $patterns;
        return $this;
    }

    /**
     * Add a forbidden pattern
     */
    public function addForbiddenPattern(string $pattern): self
    {
        $this->forbiddenPatterns[] = $pattern;
        return $this;
    }

    /**
     * Set maximum filename length
     */
    public function setMaxLength(int $length): self
    {
        $this->maxLength = $length;
        return $this;
    }

    /**
     * Set whether to allow unicode characters
     */
    public function setAllowUnicode(bool $allow): self
    {
        $this->allowUnicode = $allow;
        return $this;
    }
}
