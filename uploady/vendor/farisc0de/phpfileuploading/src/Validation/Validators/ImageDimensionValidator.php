<?php

namespace Farisc0de\PhpFileUploading\Validation\Validators;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Validation\ValidatorInterface;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Validates image dimensions
 *
 * @package PhpFileUploading
 */
class ImageDimensionValidator implements ValidatorInterface
{
    private ?int $minWidth;
    private ?int $maxWidth;
    private ?int $minHeight;
    private ?int $maxHeight;
    private ?float $minAspectRatio;
    private ?float $maxAspectRatio;

    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/tiff',
    ];

    public function __construct(
        ?int $minWidth = null,
        ?int $maxWidth = null,
        ?int $minHeight = null,
        ?int $maxHeight = null,
        ?float $minAspectRatio = null,
        ?float $maxAspectRatio = null
    ) {
        $this->minWidth = $minWidth;
        $this->maxWidth = $maxWidth;
        $this->minHeight = $minHeight;
        $this->maxHeight = $maxHeight;
        $this->minAspectRatio = $minAspectRatio;
        $this->maxAspectRatio = $maxAspectRatio;
    }

    public function validate(File $file): ValidationResult
    {
        // Check if file is an image
        try {
            $mime = $file->getMime();
            if (!in_array($mime, self::IMAGE_MIMES, true)) {
                return ValidationResult::success(['skipped' => true, 'reason' => 'Not an image file']);
            }
        } catch (\Exception $e) {
            return ValidationResult::failure(
                'Failed to determine file type',
                'MIME_DETECTION_FAILED',
                ['error' => $e->getMessage()]
            );
        }

        // Get image dimensions
        $imageInfo = @getimagesize($file->getTempName());
        if ($imageInfo === false) {
            return ValidationResult::failure(
                'Failed to read image dimensions',
                'DIMENSION_READ_FAILED',
                []
            );
        }

        [$width, $height] = $imageInfo;
        $aspectRatio = $height > 0 ? $width / $height : 0;

        $result = ValidationResult::success([
            'width' => $width,
            'height' => $height,
            'aspect_ratio' => round($aspectRatio, 4),
        ]);

        // Validate width
        if ($this->minWidth !== null && $width < $this->minWidth) {
            $result->addError(
                "Image width ({$width}px) is below minimum ({$this->minWidth}px)",
                'WIDTH_TOO_SMALL',
                ['width' => $width, 'min_width' => $this->minWidth]
            );
        }

        if ($this->maxWidth !== null && $width > $this->maxWidth) {
            $result->addError(
                "Image width ({$width}px) exceeds maximum ({$this->maxWidth}px)",
                'WIDTH_TOO_LARGE',
                ['width' => $width, 'max_width' => $this->maxWidth]
            );
        }

        // Validate height
        if ($this->minHeight !== null && $height < $this->minHeight) {
            $result->addError(
                "Image height ({$height}px) is below minimum ({$this->minHeight}px)",
                'HEIGHT_TOO_SMALL',
                ['height' => $height, 'min_height' => $this->minHeight]
            );
        }

        if ($this->maxHeight !== null && $height > $this->maxHeight) {
            $result->addError(
                "Image height ({$height}px) exceeds maximum ({$this->maxHeight}px)",
                'HEIGHT_TOO_LARGE',
                ['height' => $height, 'max_height' => $this->maxHeight]
            );
        }

        // Validate aspect ratio
        if ($this->minAspectRatio !== null && $aspectRatio < $this->minAspectRatio) {
            $result->addError(
                "Image aspect ratio ({$aspectRatio}) is below minimum ({$this->minAspectRatio})",
                'ASPECT_RATIO_TOO_SMALL',
                ['aspect_ratio' => $aspectRatio, 'min_aspect_ratio' => $this->minAspectRatio]
            );
        }

        if ($this->maxAspectRatio !== null && $aspectRatio > $this->maxAspectRatio) {
            $result->addError(
                "Image aspect ratio ({$aspectRatio}) exceeds maximum ({$this->maxAspectRatio})",
                'ASPECT_RATIO_TOO_LARGE',
                ['aspect_ratio' => $aspectRatio, 'max_aspect_ratio' => $this->maxAspectRatio]
            );
        }

        return $result;
    }

    public function getName(): string
    {
        return 'image_dimension';
    }

    /**
     * Set minimum width
     */
    public function setMinWidth(?int $width): self
    {
        $this->minWidth = $width;
        return $this;
    }

    /**
     * Set maximum width
     */
    public function setMaxWidth(?int $width): self
    {
        $this->maxWidth = $width;
        return $this;
    }

    /**
     * Set minimum height
     */
    public function setMinHeight(?int $height): self
    {
        $this->minHeight = $height;
        return $this;
    }

    /**
     * Set maximum height
     */
    public function setMaxHeight(?int $height): self
    {
        $this->maxHeight = $height;
        return $this;
    }

    /**
     * Set minimum aspect ratio
     */
    public function setMinAspectRatio(?float $ratio): self
    {
        $this->minAspectRatio = $ratio;
        return $this;
    }

    /**
     * Set maximum aspect ratio
     */
    public function setMaxAspectRatio(?float $ratio): self
    {
        $this->maxAspectRatio = $ratio;
        return $this;
    }
}
