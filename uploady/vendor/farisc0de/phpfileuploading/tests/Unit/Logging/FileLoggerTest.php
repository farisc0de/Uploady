<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\Logging;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\Logging\FileLogger;
use Farisc0de\PhpFileUploading\Logging\LogLevel;

class FileLoggerTest extends TestCase
{
    private string $logFile;
    private FileLogger $logger;

    protected function setUp(): void
    {
        $this->logFile = sys_get_temp_dir() . '/phpfileuploading_test_' . uniqid() . '.log';
        $this->logger = new FileLogger($this->logFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        // Clean up rotated files
        foreach (glob($this->logFile . '.*') as $file) {
            unlink($file);
        }
    }

    public function testLogCreatesFile(): void
    {
        $this->logger->info('Test message');

        $this->assertFileExists($this->logFile);
    }

    public function testLogWritesMessage(): void
    {
        $this->logger->info('Test message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('Test message', $content);
        $this->assertStringContainsString('[INFO]', $content);
    }

    public function testLogLevels(): void
    {
        $this->logger->debug('Debug message');
        $this->logger->info('Info message');
        $this->logger->warning('Warning message');
        $this->logger->error('Error message');

        $content = file_get_contents($this->logFile);

        $this->assertStringContainsString('[DEBUG]', $content);
        $this->assertStringContainsString('[INFO]', $content);
        $this->assertStringContainsString('[WARNING]', $content);
        $this->assertStringContainsString('[ERROR]', $content);
    }

    public function testContextInterpolation(): void
    {
        $this->logger->info('User {username} uploaded {filename}', [
            'username' => 'john',
            'filename' => 'test.jpg'
        ]);

        $content = file_get_contents($this->logFile);

        $this->assertStringContainsString('User john uploaded test.jpg', $content);
    }

    public function testMinLevelFiltering(): void
    {
        $logger = new FileLogger($this->logFile, LogLevel::WARNING);

        $logger->debug('Debug message');
        $logger->info('Info message');
        $logger->warning('Warning message');
        $logger->error('Error message');

        $content = file_get_contents($this->logFile);

        $this->assertStringNotContainsString('[DEBUG]', $content);
        $this->assertStringNotContainsString('[INFO]', $content);
        $this->assertStringContainsString('[WARNING]', $content);
        $this->assertStringContainsString('[ERROR]', $content);
    }

    public function testClear(): void
    {
        $this->logger->info('Message 1');
        $this->logger->info('Message 2');

        $this->logger->clear();

        $content = file_get_contents($this->logFile);
        $this->assertEmpty($content);
    }

    public function testGetLogFile(): void
    {
        $this->assertEquals($this->logFile, $this->logger->getLogFile());
    }

    public function testEmergencyLevel(): void
    {
        $this->logger->emergency('System failure');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[EMERGENCY]', $content);
    }

    public function testAlertLevel(): void
    {
        $this->logger->alert('Alert message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[ALERT]', $content);
    }

    public function testCriticalLevel(): void
    {
        $this->logger->critical('Critical error');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[CRITICAL]', $content);
    }

    public function testNoticeLevel(): void
    {
        $this->logger->notice('Notice message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[NOTICE]', $content);
    }

    public function testContextWithArray(): void
    {
        $this->logger->info('Data: {data}', ['data' => ['key' => 'value']]);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('{"key":"value"}', $content);
    }

    public function testContextWithBoolean(): void
    {
        $this->logger->info('Status: {status}', ['status' => true]);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('Status: true', $content);
    }

    public function testContextWithNull(): void
    {
        $this->logger->info('Value: {value}', ['value' => null]);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('Value: null', $content);
    }

    public function testExtraContextIncluded(): void
    {
        $this->logger->info('Message', ['extra' => 'data']);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('"extra":"data"', $content);
    }
}
