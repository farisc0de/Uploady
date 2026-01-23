<?php

namespace Farisc0de\PhpFileUploading\Exception;

/**
 * Exception thrown when a file is not found
 *
 * @package PhpFileUploading
 */
class FileNotFoundException extends UploadException
{
    public function __construct(string $path, ?\Throwable $previous = null)
    {
        parent::__construct(
            "File not found: {$path}",
            404,
            $previous,
            ['path' => $path]
        );
    }
}
