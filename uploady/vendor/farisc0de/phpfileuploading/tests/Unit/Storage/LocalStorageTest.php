<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Storage\LocalStorage;
use Farisc0de\PhpFileUploading\Exception\FileNotFoundException;
use Farisc0de\PhpFileUploading\Exception\StorageException;

class LocalStorageTest extends TestCase
{
    private string $testDir;
    private LocalStorage $storage;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/phpfileuploading_test_' . uniqid();
        mkdir($this->testDir, 0755, true);
        $this->storage = new LocalStorage($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDir);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function testWriteAndRead(): void
    {
        $content = 'Hello, World!';
        $this->storage->write('test.txt', $content);

        $this->assertEquals($content, $this->storage->read('test.txt'));
    }

    public function testWriteCreatesDirectory(): void
    {
        $this->storage->write('subdir/test.txt', 'content');

        $this->assertTrue($this->storage->fileExists('subdir/test.txt'));
    }

    public function testFileExists(): void
    {
        $this->assertFalse($this->storage->fileExists('nonexistent.txt'));

        $this->storage->write('exists.txt', 'content');

        $this->assertTrue($this->storage->fileExists('exists.txt'));
    }

    public function testDirectoryExists(): void
    {
        $this->assertFalse($this->storage->directoryExists('newdir'));

        $this->storage->createDirectory('newdir');

        $this->assertTrue($this->storage->directoryExists('newdir'));
    }

    public function testDelete(): void
    {
        $this->storage->write('todelete.txt', 'content');
        $this->assertTrue($this->storage->fileExists('todelete.txt'));

        $this->storage->delete('todelete.txt');

        $this->assertFalse($this->storage->fileExists('todelete.txt'));
    }

    public function testDeleteNonexistentFileDoesNotThrow(): void
    {
        // Should not throw
        $this->storage->delete('nonexistent.txt');
        $this->assertTrue(true);
    }

    public function testMove(): void
    {
        $this->storage->write('source.txt', 'content');

        $this->storage->move('source.txt', 'destination.txt');

        $this->assertFalse($this->storage->fileExists('source.txt'));
        $this->assertTrue($this->storage->fileExists('destination.txt'));
        $this->assertEquals('content', $this->storage->read('destination.txt'));
    }

    public function testCopy(): void
    {
        $this->storage->write('original.txt', 'content');

        $this->storage->copy('original.txt', 'copied.txt');

        $this->assertTrue($this->storage->fileExists('original.txt'));
        $this->assertTrue($this->storage->fileExists('copied.txt'));
        $this->assertEquals('content', $this->storage->read('copied.txt'));
    }

    public function testFileSize(): void
    {
        $content = 'Hello, World!';
        $this->storage->write('size.txt', $content);

        $this->assertEquals(strlen($content), $this->storage->fileSize('size.txt'));
    }

    public function testReadNonexistentFileThrows(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->storage->read('nonexistent.txt');
    }

    public function testMoveNonexistentFileThrows(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->storage->move('nonexistent.txt', 'dest.txt');
    }

    public function testListContents(): void
    {
        $this->storage->write('file1.txt', 'content1');
        $this->storage->write('file2.txt', 'content2');
        $this->storage->createDirectory('subdir');

        $contents = iterator_to_array($this->storage->listContents(''));

        $this->assertCount(3, $contents);
    }

    public function testListContentsDeep(): void
    {
        $this->storage->write('file1.txt', 'content1');
        $this->storage->write('subdir/file2.txt', 'content2');
        $this->storage->write('subdir/nested/file3.txt', 'content3');

        $contents = iterator_to_array($this->storage->listContents('', true));

        // Should include all files and directories
        $this->assertGreaterThanOrEqual(3, count($contents));
    }

    public function testWriteStream(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'Stream content');
        $stream = fopen($tempFile, 'rb');

        $this->storage->writeStream('streamed.txt', $stream);
        fclose($stream);
        unlink($tempFile);

        $this->assertEquals('Stream content', $this->storage->read('streamed.txt'));
    }

    public function testReadStream(): void
    {
        $this->storage->write('tostream.txt', 'Stream content');

        $stream = $this->storage->readStream('tostream.txt');
        $content = stream_get_contents($stream);
        fclose($stream);

        $this->assertEquals('Stream content', $content);
    }

    public function testDeleteDirectory(): void
    {
        $this->storage->write('dir/file1.txt', 'content1');
        $this->storage->write('dir/subdir/file2.txt', 'content2');

        $this->storage->deleteDirectory('dir');

        $this->assertFalse($this->storage->directoryExists('dir'));
    }

    public function testPublicUrl(): void
    {
        $storage = new LocalStorage($this->testDir, 0755, 0644, 'https://example.com/uploads');

        $url = $storage->publicUrl('path/to/file.jpg');

        $this->assertEquals('https://example.com/uploads/path/to/file.jpg', $url);
    }

    public function testGetRootPath(): void
    {
        $this->assertEquals($this->testDir, $this->storage->getRootPath());
    }
}
