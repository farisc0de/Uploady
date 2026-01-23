<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Validation\Validators;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\Validation\Validators\FilenameValidator;

class FilenameValidatorTest extends TestCase
{
    private function createMockFile(string $filename): File
    {
        $fileData = [
            'name' => $filename,
            'type' => 'application/octet-stream',
            'tmp_name' => '/tmp/test',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        return new File($fileData, new Utility());
    }

    public function testValidFilenamePasses(): void
    {
        $validator = new FilenameValidator();
        $file = $this->createMockFile('valid-file.jpg');

        $result = $validator->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function testForbiddenNameFails(): void
    {
        $validator = new FilenameValidator(['shell.php', 'backdoor.php']);
        $file = $this->createMockFile('shell.php');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('FORBIDDEN_FILENAME', $result->getErrors()[0]->getCode());
    }

    public function testForbiddenNameCaseInsensitive(): void
    {
        $validator = new FilenameValidator(['shell.php']);
        $file = $this->createMockFile('SHELL.PHP');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
    }

    public function testForbiddenPatternFails(): void
    {
        $validator = new FilenameValidator([], ['/\.php$/i']);
        $file = $this->createMockFile('test.php');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('FORBIDDEN_PATTERN', $result->getErrors()[0]->getCode());
    }

    public function testFilenameTooLongFails(): void
    {
        $validator = new FilenameValidator([], [], 10);
        $file = $this->createMockFile('verylongfilename.jpg');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('FILENAME_TOO_LONG', $result->getErrors()[0]->getCode());
    }

    public function testPathTraversalDetected(): void
    {
        $validator = new FilenameValidator();
        $file = $this->createMockFile('../../../etc/passwd');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('PATH_TRAVERSAL_DETECTED', $result->getErrors()[0]->getCode());
    }

    public function testNullByteDetected(): void
    {
        $validator = new FilenameValidator();
        $file = $this->createMockFile("test\0.php.jpg");

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('NULL_BYTE_DETECTED', $result->getErrors()[0]->getCode());
    }

    public function testWindowsReservedNameWarning(): void
    {
        $validator = new FilenameValidator();
        $file = $this->createMockFile('CON.txt');

        $result = $validator->validate($file);

        $this->assertTrue($result->isValid()); // Warning doesn't fail validation
        $this->assertTrue($result->hasWarnings());
        $this->assertEquals('RESERVED_WINDOWS_NAME', $result->getWarnings()[0]->getCode());
    }

    public function testGetName(): void
    {
        $validator = new FilenameValidator();

        $this->assertEquals('filename', $validator->getName());
    }
}
