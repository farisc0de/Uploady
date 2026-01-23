<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Migrations;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Migrations\MigrationCreator;

class MigrationCreatorTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/phpmigration_test_' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    protected function removeDirectory(string $dir): void
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

    public function testCanCreateBlankMigration(): void
    {
        $creator = new MigrationCreator();
        $path = $creator->create('test_migration', $this->tempDir);

        $this->assertFileExists($path);
        $this->assertStringContainsString('test_migration', $path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('MigrationInterface', $content);
        $this->assertStringContainsString('function up', $content);
        $this->assertStringContainsString('function down', $content);
    }

    public function testCanCreateTableMigration(): void
    {
        $creator = new MigrationCreator();
        $path = $creator->create('create_users_table', $this->tempDir, 'users', true);

        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString("'users'", $content);
        $this->assertStringContainsString('$table->id()', $content);
        $this->assertStringContainsString('$table->timestamps()', $content);
        $this->assertStringContainsString('dropIfExists', $content);
    }

    public function testCanCreateUpdateMigration(): void
    {
        $creator = new MigrationCreator();
        $path = $creator->create('add_email_to_users', $this->tempDir, 'users', false);

        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString("'users'", $content);
        $this->assertStringContainsString('$schema->table', $content);
    }

    public function testMigrationFilenameHasTimestamp(): void
    {
        $creator = new MigrationCreator();
        $path = $creator->create('test_migration', $this->tempDir);

        $filename = basename($path);
        $this->assertMatchesRegularExpression('/^\d{4}_\d{2}_\d{2}_\d{6}_test_migration\.php$/', $filename);
    }

    public function testThrowsExceptionForDuplicateMigration(): void
    {
        $creator = new MigrationCreator();
        $creator->create('duplicate_migration', $this->tempDir);

        $this->expectException(\InvalidArgumentException::class);
        $creator->create('duplicate_migration', $this->tempDir);
    }

    public function testCreatesDirectoryIfNotExists(): void
    {
        $creator = new MigrationCreator();
        $newDir = $this->tempDir . '/nested/migrations';

        $path = $creator->create('test_migration', $newDir);

        $this->assertFileExists($path);
        $this->assertDirectoryExists($newDir);
    }
}
