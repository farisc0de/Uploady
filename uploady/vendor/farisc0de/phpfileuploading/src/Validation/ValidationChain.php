<?php

namespace Farisc0de\PhpFileUploading\Validation;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;
use Farisc0de\PhpFileUploading\Logging\LogLevel;

/**
 * Chains multiple validators together
 *
 * @package PhpFileUploading
 */
class ValidationChain implements ValidatorInterface
{
    use LoggerAwareTrait;

    /** @var ValidatorInterface[] */
    private array $validators = [];
    private bool $stopOnFirstError;

    public function __construct(bool $stopOnFirstError = false)
    {
        $this->stopOnFirstError = $stopOnFirstError;
    }

    /**
     * Add a validator to the chain
     */
    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[$validator->getName()] = $validator;
        return $this;
    }

    /**
     * Remove a validator from the chain
     */
    public function removeValidator(string $name): self
    {
        unset($this->validators[$name]);
        return $this;
    }

    /**
     * Check if a validator exists in the chain
     */
    public function hasValidator(string $name): bool
    {
        return isset($this->validators[$name]);
    }

    /**
     * Get a validator by name
     */
    public function getValidator(string $name): ?ValidatorInterface
    {
        return $this->validators[$name] ?? null;
    }

    /**
     * Get all validators
     *
     * @return ValidatorInterface[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * Validate a file through all validators in the chain
     */
    public function validate(File $file): ValidationResult
    {
        $result = ValidationResult::success();
        $filename = $file->getName();

        $this->logDebug('Starting validation chain for file: {filename}', ['filename' => $filename]);

        foreach ($this->validators as $name => $validator) {
            $this->logDebug('Running validator: {validator}', ['validator' => $name]);

            $validatorResult = $validator->validate($file);
            $result->merge($validatorResult);

            if ($validatorResult->isFailed()) {
                $this->logWarning('Validator {validator} failed for file {filename}: {error}', [
                    'validator' => $name,
                    'filename' => $filename,
                    'error' => $validatorResult->getFirstError(),
                ]);

                if ($this->stopOnFirstError) {
                    break;
                }
            } else {
                $this->logDebug('Validator {validator} passed', ['validator' => $name]);
            }
        }

        if ($result->isValid()) {
            $this->logInfo('File {filename} passed all validations', ['filename' => $filename]);
        } else {
            $this->logWarning('File {filename} failed validation with {count} errors', [
                'filename' => $filename,
                'count' => count($result->getErrors()),
            ]);
        }

        return $result;
    }

    public function getName(): string
    {
        return 'chain';
    }

    /**
     * Set whether to stop on first error
     */
    public function setStopOnFirstError(bool $stop): self
    {
        $this->stopOnFirstError = $stop;
        return $this;
    }

    /**
     * Clear all validators
     */
    public function clear(): self
    {
        $this->validators = [];
        return $this;
    }

    /**
     * Get the count of validators
     */
    public function count(): int
    {
        return count($this->validators);
    }
}
