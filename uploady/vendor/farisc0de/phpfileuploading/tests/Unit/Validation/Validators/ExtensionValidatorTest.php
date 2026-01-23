<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Validation\Validators;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\Validation\Validators\ExtensionValidator;

class ExtensionValidatorTest extends TestCase
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

    public function testAllowedExtensionPasses(): void
    {
        $validator = new ExtensionValidator(['jpg', 'png', 'gif']);
        $file = $this->createMockFile('test.jpg');

        $result = $validator->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function testDisallowedExtensionFails(): void
    {
        $validator = new ExtensionValidator(['jpg', 'png', 'gif']);
        $file = $this->createMockFile('test.php');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('INVALID_EXTENSION', $result->getErrors()[0]->getCode());
    }

    public function testBlockedExtensionFails(): void
    {
        $validator = new ExtensionValidator([], ['php', 'exe', 'sh']);
        $file = $this->createMockFile('test.php');

        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('BLOCKED_EXTENSION', $result->getErrors()[0]->getCode());
    }

    public function testCaseInsensitiveExtension(): void
    {
        $validator = new ExtensionValidator(['jpg', 'png']);
        $file = $this->createMockFile('test.JPG');

        $result = $validator->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function testEmptyAllowedListAllowsAll(): void
    {
        $validator = new ExtensionValidator([]);
        $file = $this->createMockFile('test.anything');

        $result = $validator->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function testGetName(): void
    {
        $validator = new ExtensionValidator();

        $this->assertEquals('extension', $validator->getName());
    }

    public function testAddAllowedExtension(): void
    {
        $validator = new ExtensionValidator(['jpg']);
        $validator->addAllowedExtension('png');

        $file = $this->createMockFile('test.png');
        $result = $validator->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function testAddBlockedExtension(): void
    {
        $validator = new ExtensionValidator();
        $validator->addBlockedExtension('exe');

        $file = $this->createMockFile('test.exe');
        $result = $validator->validate($file);

        $this->assertFalse($result->isValid());
    }
}
