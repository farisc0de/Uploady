<?php

namespace Farisc0de\PhpFileUploading;

use InvalidArgumentException;
use RuntimeException;
use GdImage;

/**
 * Image Handler Class
 *
 * Provides comprehensive image manipulation functionality including
 * resizing, cropping, watermarking, and format conversion.
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */
class Image
{
    /**
     * Supported image MIME types and their corresponding GD creation functions
     */
    private const SUPPORTED_TYPES = [
        'image/jpeg' => ['create' => 'imagecreatefromjpeg', 'output' => 'imagejpeg'],
        'image/png'  => ['create' => 'imagecreatefrompng',  'output' => 'imagepng'],
        'image/gif'  => ['create' => 'imagecreatefromgif',  'output' => 'imagegif'],
        'image/webp' => ['create' => 'imagecreatefromwebp', 'output' => 'imagewebp'],
    ];

    /**
     * Default quality settings for different image types
     */
    private const DEFAULT_QUALITY = [
        'image/jpeg' => 85,
        'image/png'  => 6,  // PNG compression level (0-9)
        'image/webp' => 80,
    ];

    /**
     * Encode image to base64
     * 
     * @param string $imagePath Path to the image file
     * @return string Base64 encoded image with data URI scheme
     * @throws RuntimeException If image cannot be read or encoded
     */
    public function encodeToBase64(string $imagePath): string
    {
        if (!file_exists($imagePath)) {
            throw new RuntimeException("Image file not found: {$imagePath}");
        }

        $imageData = file_get_contents($imagePath);
        if ($imageData === false) {
            throw new RuntimeException("Failed to read image file: {$imagePath}");
        }

        $mimeType = mime_content_type($imagePath);
        if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
            throw new RuntimeException("Invalid image file: {$imagePath}");
        }

        return "data:{$mimeType};base64," . base64_encode($imageData);
    }

    /**
     * Compress image with specified quality
     *
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param int $quality Quality level (0-100 for JPEG/WEBP, 0-9 for PNG)
     * @param string|null $outputFormat Optional output format (e.g., 'webp', 'jpeg')
     * @return bool True if successful
     * @throws RuntimeException If compression fails
     */
    public function compress(
        string $source,
        string $destination,
        int $quality,
        ?string $outputFormat = null
    ): bool {
        $this->validateImageFile($source);
        
        $imageInfo = getimagesize($source);
        if ($imageInfo === false) {
            throw new RuntimeException("Failed to get image information: {$source}");
        }

        $mimeType = $imageInfo['mime'];
        if (!isset(self::SUPPORTED_TYPES[$mimeType])) {
            throw new RuntimeException("Unsupported image type: {$mimeType}");
        }

        $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
        $image = $createFunc($source);
        
        if (!$image instanceof GdImage) {
            throw new RuntimeException("Failed to create image resource");
        }

        // If output format is specified, convert to that format
        if ($outputFormat !== null) {
            $outputMime = "image/{$outputFormat}";
            if (!isset(self::SUPPORTED_TYPES[$outputMime])) {
                throw new RuntimeException("Unsupported output format: {$outputFormat}");
            }
            $outputFunc = self::SUPPORTED_TYPES[$outputMime]['output'];
            $quality = $this->normalizeQuality($quality, $outputMime);
        } else {
            $outputFunc = self::SUPPORTED_TYPES[$mimeType]['output'];
            $quality = $this->normalizeQuality($quality, $mimeType);
        }

        $result = $outputFunc($image, $destination, $quality);
        imagedestroy($image);

        if (!$result) {
            throw new RuntimeException("Failed to save compressed image");
        }

        return true;
    }

    /**
     * Resize an image while maintaining aspect ratio
     *
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @param int $quality Output quality
     * @return bool True if successful
     * @throws RuntimeException If resizing fails
     */
    public function resize(
        string $source,
        string $destination,
        int $maxWidth,
        int $maxHeight,
        int $quality = 85
    ): bool {
        $this->validateImageFile($source);
        
        $imageInfo = getimagesize($source);
        if ($imageInfo === false) {
            throw new RuntimeException("Failed to get image information");
        }

        [$width, $height] = $imageInfo;
        $mimeType = $imageInfo['mime'];

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create image resource
        $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
        $srcImage = $createFunc($source);
        
        if (!$srcImage instanceof GdImage) {
            throw new RuntimeException("Failed to create source image resource");
        }

        // Create new image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        if (!$dstImage instanceof GdImage) {
            imagedestroy($srcImage);
            throw new RuntimeException("Failed to create destination image resource");
        }

        // Handle transparency for PNG images
        if ($mimeType === 'image/png') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }

        // Resize
        if (!imagecopyresampled(
            $dstImage,
            $srcImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        )) {
            imagedestroy($srcImage);
            imagedestroy($dstImage);
            throw new RuntimeException("Failed to resize image");
        }

        // Save
        $outputFunc = self::SUPPORTED_TYPES[$mimeType]['output'];
        $quality = $this->normalizeQuality($quality, $mimeType);
        $result = $outputFunc($dstImage, $destination, $quality);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if (!$result) {
            throw new RuntimeException("Failed to save resized image");
        }

        return true;
    }

    /**
     * Add a watermark to an image
     *
     * @param string $source Source image path
     * @param string $watermark Watermark image path
     * @param string $destination Destination image path
     * @param string $position Position of watermark (top-left, top-right, bottom-left, bottom-right, center)
     * @param int $opacity Watermark opacity (0-100)
     * @param int $padding Padding from edges
     * @return bool True if successful
     * @throws RuntimeException If watermarking fails
     */
    public function addWatermark(
        string $source,
        string $watermark,
        string $destination,
        string $position = 'bottom-right',
        int $opacity = 50,
        int $padding = 10
    ): bool {
        $this->validateImageFile($source);
        $this->validateImageFile($watermark);

        // Get source image info
        $sourceInfo = getimagesize($source);
        $watermarkInfo = getimagesize($watermark);
        
        if ($sourceInfo === false || $watermarkInfo === false) {
            throw new RuntimeException("Failed to get image information");
        }

        // Create image resources
        $sourceImage = $this->createImageResource($source, $sourceInfo['mime']);
        $watermarkImage = $this->createImageResource($watermark, $watermarkInfo['mime']);

        // Calculate position
        $x = $y = $padding;
        switch ($position) {
            case 'top-right':
                $x = $sourceInfo[0] - $watermarkInfo[0] - $padding;
                break;
            case 'bottom-left':
                $y = $sourceInfo[1] - $watermarkInfo[1] - $padding;
                break;
            case 'bottom-right':
                $x = $sourceInfo[0] - $watermarkInfo[0] - $padding;
                $y = $sourceInfo[1] - $watermarkInfo[1] - $padding;
                break;
            case 'center':
                $x = ($sourceInfo[0] - $watermarkInfo[0]) / 2;
                $y = ($sourceInfo[1] - $watermarkInfo[1]) / 2;
                break;
        }

        // Apply opacity
        if ($opacity < 100) {
            imagealphablending($watermarkImage, false);
            imagesavealpha($watermarkImage, true);
            imagefilter($watermarkImage, IMG_FILTER_COLORIZE, 0, 0, 0, (127 * (100 - $opacity)) / 100);
        }

        // Merge images
        if (!imagecopy(
            $sourceImage,
            $watermarkImage,
            (int)$x,
            (int)$y,
            0,
            0,
            $watermarkInfo[0],
            $watermarkInfo[1]
        )) {
            imagedestroy($sourceImage);
            imagedestroy($watermarkImage);
            throw new RuntimeException("Failed to apply watermark");
        }

        // Save result
        $outputFunc = self::SUPPORTED_TYPES[$sourceInfo['mime']]['output'];
        $quality = self::DEFAULT_QUALITY[$sourceInfo['mime']] ?? 85;
        $result = $outputFunc($sourceImage, $destination, $quality);

        imagedestroy($sourceImage);
        imagedestroy($watermarkImage);

        if (!$result) {
            throw new RuntimeException("Failed to save watermarked image");
        }

        return true;
    }

    /**
     * Convert image to another format
     *
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param string $format Output format (jpeg, png, gif, webp)
     * @param int $quality Output quality
     * @return bool True if successful
     * @throws RuntimeException If conversion fails
     */
    public function convert(
        string $source,
        string $destination,
        string $format,
        int $quality = 85
    ): bool {
        $format = strtolower($format);
        $outputMime = "image/{$format}";
        
        if (!isset(self::SUPPORTED_TYPES[$outputMime])) {
            throw new InvalidArgumentException("Unsupported output format: {$format}");
        }

        return $this->compress($source, $destination, $quality, $format);
    }

    /**
     * Apply a filter to an image
     *
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param int $filter Filter type (IMG_FILTER_* constants)
     * @param array $args Filter arguments
     * @return bool True if successful
     * @throws RuntimeException If filter application fails
     */
    public function applyFilter(
        string $source,
        string $destination,
        int $filter,
        array $args = []
    ): bool {
        $this->validateImageFile($source);
        
        $imageInfo = getimagesize($source);
        if ($imageInfo === false) {
            throw new RuntimeException("Failed to get image information");
        }

        $image = $this->createImageResource($source, $imageInfo['mime']);

        // Apply filter
        if (!imagefilter($image, $filter, ...$args)) {
            imagedestroy($image);
            throw new RuntimeException("Failed to apply image filter");
        }

        // Save result
        $outputFunc = self::SUPPORTED_TYPES[$imageInfo['mime']]['output'];
        $quality = self::DEFAULT_QUALITY[$imageInfo['mime']] ?? 85;
        $result = $outputFunc($image, $destination, $quality);

        imagedestroy($image);

        if (!$result) {
            throw new RuntimeException("Failed to save filtered image");
        }

        return true;
    }

    /**
     * Get image information
     *
     * @param string $source Source image path
     * @return array Image information including dimensions, type, and size
     * @throws RuntimeException If image information cannot be retrieved
     */
    public function getInfo(string $source): array
    {
        $this->validateImageFile($source);
        
        $imageInfo = getimagesize($source);
        if ($imageInfo === false) {
            throw new RuntimeException("Failed to get image information");
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageInfo['mime'],
            'size' => filesize($source),
            'aspectRatio' => $imageInfo[0] / $imageInfo[1],
        ];
    }

    /**
     * Validate image file
     *
     * @param string $path Image file path
     * @throws RuntimeException If file is invalid or inaccessible
     */
    private function validateImageFile(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Image file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new RuntimeException("Image file is not readable: {$path}");
        }

        $mimeType = mime_content_type($path);
        if (!$mimeType || !isset(self::SUPPORTED_TYPES[$mimeType])) {
            throw new RuntimeException("Unsupported image type: {$mimeType}");
        }
    }

    /**
     * Create image resource from file
     *
     * @param string $path Image file path
     * @param string $mimeType Image MIME type
     * @return GdImage Image resource
     * @throws RuntimeException If resource creation fails
     */
    private function createImageResource(string $path, string $mimeType): GdImage
    {
        $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
        $image = $createFunc($path);
        
        if (!$image instanceof GdImage) {
            throw new RuntimeException("Failed to create image resource");
        }

        return $image;
    }

    /**
     * Normalize quality value based on image type
     *
     * @param int $quality Input quality value
     * @param string $mimeType Image MIME type
     * @return int Normalized quality value
     */
    private function normalizeQuality(int $quality, string $mimeType): int
    {
        if ($mimeType === 'image/png') {
            // PNG quality is 0-9
            return min(9, max(0, (int)($quality / 11.111)));
        }
        
        // JPEG/WEBP quality is 0-100
        return min(100, max(0, $quality));
    }
}
