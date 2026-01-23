<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Utility;
use InvalidArgumentException;

class UtilityTest extends TestCase
{
    private Utility $utility;

    protected function setUp(): void
    {
        $this->utility = new Utility();
    }

    public function testSizeInBytesWithBytes(): void
    {
        $this->assertEquals(100, $this->utility->sizeInBytes('100 B'));
        $this->assertEquals(100, $this->utility->sizeInBytes('100B'));
    }

    public function testSizeInBytesWithKilobytes(): void
    {
        $this->assertEquals(1024, $this->utility->sizeInBytes('1 KB'));
        $this->assertEquals(2048, $this->utility->sizeInBytes('2KB'));
    }

    public function testSizeInBytesWithMegabytes(): void
    {
        $this->assertEquals(1048576, $this->utility->sizeInBytes('1 MB'));
        $this->assertEquals(5242880, $this->utility->sizeInBytes('5MB'));
    }

    public function testSizeInBytesWithGigabytes(): void
    {
        $this->assertEquals(1073741824, $this->utility->sizeInBytes('1 GB'));
    }

    public function testSizeInBytesWithInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->utility->sizeInBytes('invalid');
    }

    public function testFormatBytesWithZero(): void
    {
        $this->assertEquals('0 B', $this->utility->formatBytes(0));
    }

    public function testFormatBytesWithBytes(): void
    {
        $this->assertEquals('500.00 B', $this->utility->formatBytes(500));
    }

    public function testFormatBytesWithKilobytes(): void
    {
        $this->assertEquals('1.00 KB', $this->utility->formatBytes(1024));
    }

    public function testFormatBytesWithMegabytes(): void
    {
        $this->assertEquals('1.00 MB', $this->utility->formatBytes(1048576));
    }

    public function testFormatBytesWithCustomPrecision(): void
    {
        $this->assertEquals('1.5 KB', $this->utility->formatBytes(1536, 1));
    }

    public function testSanitizeRemovesHtmlTags(): void
    {
        $this->assertEquals('Hello World', $this->utility->sanitize('<script>Hello World</script>'));
    }

    public function testSanitizeTrimsWhitespace(): void
    {
        $this->assertEquals('Hello', $this->utility->sanitize('  Hello  '));
    }

    public function testSanitizeWithNull(): void
    {
        $this->assertNull($this->utility->sanitize(null));
    }

    public function testSanitizeEncodesSpecialChars(): void
    {
        $result = $this->utility->sanitize('Hello & "World"');
        $this->assertStringContainsString('&amp;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    public function testFixIntOverflowWithPositiveValue(): void
    {
        $this->assertEquals(100.0, $this->utility->fixIntOverflow(100));
    }

    public function testConvertUnitToKilobytes(): void
    {
        $bytes = 2048;
        $this->assertEquals(2.0, $this->utility->convertUnit($bytes, 'KB'));
    }

    public function testConvertUnitToMegabytes(): void
    {
        $bytes = 2097152;
        $this->assertEquals(2.0, $this->utility->convertUnit($bytes, 'MB'));
    }

    public function testNormalizeFileArrayWithSingleFile(): void
    {
        $filePost = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/phpXXX',
            'error' => 0,
            'size' => 1024
        ];

        $result = $this->utility->normalizeFileArray($filePost);

        $this->assertCount(1, $result);
        $this->assertEquals('test.jpg', $result[0]['name']);
    }

    public function testNormalizeFileArrayWithMultipleFiles(): void
    {
        $filePost = [
            'name' => ['test1.jpg', 'test2.jpg'],
            'type' => ['image/jpeg', 'image/jpeg'],
            'tmp_name' => ['/tmp/php1', '/tmp/php2'],
            'error' => [0, 0],
            'size' => [1024, 2048]
        ];

        $result = $this->utility->normalizeFileArray($filePost);

        $this->assertCount(2, $result);
        $this->assertEquals('test1.jpg', $result[0]['name']);
        $this->assertEquals('test2.jpg', $result[1]['name']);
    }

    public function testNormalizeFileArrayWithAlreadyNormalized(): void
    {
        $filePost = [
            [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/phpXXX',
                'error' => 0,
                'size' => 1024
            ]
        ];

        $result = $this->utility->normalizeFileArray($filePost);

        $this->assertCount(1, $result);
        $this->assertEquals('test.jpg', $result[0]['name']);
    }
}
