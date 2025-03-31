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
     * Constructor - Validates that GD extension is available
     * 
     * @throws RuntimeException If GD extension is not available
     */
    public function __construct()
    {
        if (!extension_loaded('gd')) {
            throw new RuntimeException('GD extension is not available. Please install or enable it.');
        }
    }

    /**
     * Encode image to base64
     * 
     * @param string $imagePath Path to the image file
     * @return string Base64 encoded image with data URI scheme
     * @throws RuntimeException If image cannot be read or encoded
     */
    public function encodeToBase64(string $imagePath): string
    {
        $this->validateImageFile($imagePath);

        $imageData = file_get_contents($imagePath);
        if ($imageData === false) {
            throw new RuntimeException("Failed to read image file: {$imagePath}");
        }

        $mimeType = $this->getMimeType($imagePath);
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

        $srcImage = null;
        $dstImage = null;
        
        try {
            $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
            $srcImage = $createFunc($source);
            
            if (!$srcImage instanceof GdImage) {
                throw new RuntimeException("Failed to create image resource");
            }
            
            // Determine output format and function
            $outputMime = $mimeType;
            if ($outputFormat !== null) {
                $outputMime = "image/{$outputFormat}";
                if (!isset(self::SUPPORTED_TYPES[$outputMime])) {
                    throw new RuntimeException("Unsupported output format: {$outputFormat}");
                }
            }
            
            $outputFunc = self::SUPPORTED_TYPES[$outputMime]['output'];
            $normalizedQuality = $this->normalizeQuality($quality, $outputMime);
            
            // Handle transparency for PNG images
            if ($outputMime === 'image/png') {
                imagealphablending($srcImage, false);
                imagesavealpha($srcImage, true);
            }
            
            // Save with specified quality
            $result = $outputFunc($srcImage, $destination, $normalizedQuality);
            
            if (!$result) {
                throw new RuntimeException("Failed to save compressed image");
            }
            
            return true;
        } finally {
            // Clean up resources
            if ($srcImage instanceof GdImage) {
                imagedestroy($srcImage);
            }
            if ($dstImage instanceof GdImage) {
                imagedestroy($dstImage);
            }
        }
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

        $srcImage = null;
        $dstImage = null;
        
        try {
            // Create image resource
            $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
            $srcImage = $createFunc($source);
            
            if (!$srcImage instanceof GdImage) {
                throw new RuntimeException("Failed to create source image resource");
            }

            // Create new image
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);
            if (!$dstImage instanceof GdImage) {
                throw new RuntimeException("Failed to create destination image resource");
            }

            // Handle transparency for PNG and GIF images
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
                $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
                imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
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
                throw new RuntimeException("Failed to resize image");
            }

            // Save
            $outputFunc = self::SUPPORTED_TYPES[$mimeType]['output'];
            $normalizedQuality = $this->normalizeQuality($quality, $mimeType);
            $result = $outputFunc($dstImage, $destination, $normalizedQuality);

            if (!$result) {
                throw new RuntimeException("Failed to save resized image");
            }

            return true;
        } finally {
            // Clean up resources
            if ($srcImage instanceof GdImage) {
                imagedestroy($srcImage);
            }
            if ($dstImage instanceof GdImage) {
                imagedestroy($dstImage);
            }
        }
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

        $sourceImage = null;
        $watermarkImage = null;
        
        try {
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
                default:
                    throw new InvalidArgumentException("Invalid position: {$position}");
            }

            // Apply opacity
            if ($opacity < 100) {
                imagealphablending($watermarkImage, false);
                imagesavealpha($watermarkImage, true);
                imagefilter($watermarkImage, IMG_FILTER_COLORIZE, 0, 0, 0, (127 * (100 - $opacity)) / 100);
            }

            // Preserve transparency in the source image if it's PNG
            if ($sourceInfo['mime'] === 'image/png') {
                imagealphablending($sourceImage, true);
                imagesavealpha($sourceImage, true);
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
                throw new RuntimeException("Failed to apply watermark");
            }

            // Save result
            $outputFunc = self::SUPPORTED_TYPES[$sourceInfo['mime']]['output'];
            $quality = self::DEFAULT_QUALITY[$sourceInfo['mime']] ?? 85;
            $result = $outputFunc($sourceImage, $destination, $quality);

            if (!$result) {
                throw new RuntimeException("Failed to save watermarked image");
            }

            return true;
        } finally {
            // Clean up resources
            if ($sourceImage instanceof GdImage) {
                imagedestroy($sourceImage);
            }
            if ($watermarkImage instanceof GdImage) {
                imagedestroy($watermarkImage);
            }
        }
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

        $image = null;
        
        try {
            $image = $this->createImageResource($source, $imageInfo['mime']);

            // Apply filter
            if (!imagefilter($image, $filter, ...$args)) {
                throw new RuntimeException("Failed to apply image filter");
            }

            // Save result
            $outputFunc = self::SUPPORTED_TYPES[$imageInfo['mime']]['output'];
            $quality = self::DEFAULT_QUALITY[$imageInfo['mime']] ?? 85;
            $result = $outputFunc($image, $destination, $quality);

            if (!$result) {
                throw new RuntimeException("Failed to save filtered image");
            }

            return true;
        } finally {
            // Clean up resources
            if ($image instanceof GdImage) {
                imagedestroy($image);
            }
        }
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

        $fileSize = filesize($source);
        if ($fileSize === false) {
            throw new RuntimeException("Failed to get file size");
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageInfo['mime'],
            'size' => $fileSize,
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
        if (!extension_loaded('gd')) {
            throw new RuntimeException('GD extension is not available. Please install or enable it.');
        }
        
        if (!file_exists($path)) {
            throw new RuntimeException("Image file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new RuntimeException("Image file is not readable: {$path}");
        }

        $mimeType = $this->getMimeType($path);
        if (!$mimeType || !isset(self::SUPPORTED_TYPES[$mimeType])) {
            throw new RuntimeException("Unsupported image type: {$mimeType}");
        }
    }

    /**
     * Get MIME type of a file with fallbacks
     * 
     * @param string $path File path
     * @return string|false MIME type or false on failure
     */
    private function getMimeType(string $path): string|false
    {
        // Try mime_content_type first
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($path);
            if ($mimeType !== false) {
                return $mimeType;
            }
        }
        
        // Try fileinfo extension
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $path);
            finfo_close($finfo);
            if ($mimeType !== false) {
                return $mimeType;
            }
        }
        
        // Try to determine from file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $extensionMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        return $extensionMap[$extension] ?? false;
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
        if (!isset(self::SUPPORTED_TYPES[$mimeType])) {
            throw new RuntimeException("Unsupported image type: {$mimeType}");
        }
        
        $createFunc = self::SUPPORTED_TYPES[$mimeType]['create'];
        if (!function_exists($createFunc)) {
            throw new RuntimeException("GD function {$createFunc} is not available");
        }
        
        // Set memory limit temporarily for large images
        $memoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '256M');
        
        try {
            // Suppress warnings and convert to exceptions
            $image = @$createFunc($path);
            
            if (!$image instanceof GdImage) {
                throw new RuntimeException("Failed to create image resource from {$path}");
            }
            
            // Handle transparency for PNG images
            if ($mimeType === 'image/png') {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
            
            return $image;
        } finally {
            // Restore original memory limit
            ini_set('memory_limit', $memoryLimit);
        }
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
            // PNG quality is 0-9 (compression level)
            return min(9, max(0, (int)round($quality / 11.1)));
        }
        
        // JPEG/WEBP quality is 0-100
        return min(100, max(0, $quality));
    }
}
