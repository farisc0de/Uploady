<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Exception\ValidationException;

class ValidationExceptionTest extends TestCase
{
    public function testInvalidExtension(): void
    {
        $exception = ValidationException::invalidExtension('php', ['jpg', 'png']);

        $this->assertEquals(ValidationException::CODE_INVALID_EXTENSION, $exception->getCode());
        $this->assertEquals('extension', $exception->getValidationType());
        $this->assertStringContainsString('php', $exception->getMessage());
        $this->assertEquals('php', $exception->getContext()['extension']);
        $this->assertEquals(['jpg', 'png'], $exception->getContext()['allowed']);
    }

    public function testInvalidMime(): void
    {
        $exception = ValidationException::invalidMime('text/php', 'image/jpeg');

        $this->assertEquals(ValidationException::CODE_INVALID_MIME, $exception->getCode());
        $this->assertEquals('mime', $exception->getValidationType());
        $this->assertStringContainsString('text/php', $exception->getMessage());
        $this->assertStringContainsString('image/jpeg', $exception->getMessage());
    }

    public function testFileTooLarge(): void
    {
        $exception = ValidationException::fileTooLarge(2000000, 1000000);

        $this->assertEquals(ValidationException::CODE_FILE_TOO_LARGE, $exception->getCode());
        $this->assertEquals('size', $exception->getValidationType());
        $this->assertEquals(2000000, $exception->getContext()['size']);
        $this->assertEquals(1000000, $exception->getContext()['max_size']);
    }

    public function testFileTooSmall(): void
    {
        $exception = ValidationException::fileTooSmall(100, 1000);

        $this->assertEquals(ValidationException::CODE_FILE_TOO_SMALL, $exception->getCode());
        $this->assertEquals('size', $exception->getValidationType());
    }

    public function testForbiddenName(): void
    {
        $exception = ValidationException::forbiddenName('shell.php');

        $this->assertEquals(ValidationException::CODE_FORBIDDEN_NAME, $exception->getCode());
        $this->assertEquals('forbidden', $exception->getValidationType());
        $this->assertStringContainsString('shell.php', $exception->getMessage());
    }

    public function testEmptyFile(): void
    {
        $exception = ValidationException::emptyFile();

        $this->assertEquals(ValidationException::CODE_EMPTY_FILE, $exception->getCode());
        $this->assertEquals('empty', $exception->getValidationType());
    }

    public function testInvalidDimensions(): void
    {
        $exception = ValidationException::invalidDimensions(2000, 1500, 1920, 1080);

        $this->assertEquals(ValidationException::CODE_INVALID_DIMENSIONS, $exception->getCode());
        $this->assertEquals('dimensions', $exception->getValidationType());
        $this->assertEquals(2000, $exception->getContext()['width']);
        $this->assertEquals(1500, $exception->getContext()['height']);
    }

    public function testNotAnImage(): void
    {
        $exception = ValidationException::notAnImage('application/pdf');

        $this->assertEquals(ValidationException::CODE_NOT_AN_IMAGE, $exception->getCode());
        $this->assertEquals('image', $exception->getValidationType());
    }

    public function testVirusDetected(): void
    {
        $exception = ValidationException::virusDetected('malware.exe', 'Trojan.Generic');

        $this->assertEquals(ValidationException::CODE_VIRUS_DETECTED, $exception->getCode());
        $this->assertEquals('virus', $exception->getValidationType());
        $this->assertStringContainsString('malware.exe', $exception->getMessage());
        $this->assertStringContainsString('Trojan.Generic', $exception->getMessage());
    }

    public function testRateLimitExceeded(): void
    {
        $exception = ValidationException::rateLimitExceeded('192.168.1.1', 10, 60);

        $this->assertEquals(ValidationException::CODE_RATE_LIMIT_EXCEEDED, $exception->getCode());
        $this->assertEquals('rate_limit', $exception->getValidationType());
        $this->assertEquals(10, $exception->getContext()['limit']);
        $this->assertEquals(60, $exception->getContext()['window']);
    }
}
