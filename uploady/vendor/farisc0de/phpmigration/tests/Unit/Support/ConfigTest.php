<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Support\Config;

class ConfigTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/phpmigration_config_test_' . uniqid();
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

    public function testCanCreateConfigWithArray(): void
    {
        $config = new Config(['key' => 'value']);
        $this->assertEquals('value', $config->get('key'));
    }

    public function testCanGetDefaultValue(): void
    {
        $config = new Config();
        $this->assertEquals('default', $config->get('nonexistent', 'default'));
    }

    public function testCanSetValue(): void
    {
        $config = new Config();
        $config->set('key', 'value');
        $this->assertEquals('value', $config->get('key'));
    }

    public function testCanGetNestedValue(): void
    {
        $config = new Config([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => ['host' => 'localhost'],
                ],
            ],
        ]);

        $this->assertEquals('mysql', $config->get('database.default'));
        $this->assertEquals('localhost', $config->get('database.connections.mysql.host'));
    }

    public function testCanSetNestedValue(): void
    {
        $config = new Config();
        $config->set('database.default', 'pgsql');

        $this->assertEquals('pgsql', $config->get('database.default'));
    }

    public function testCanCheckIfKeyExists(): void
    {
        $config = new Config(['key' => 'value']);

        $this->assertTrue($config->has('key'));
        $this->assertFalse($config->has('nonexistent'));
    }

    public function testCanLoadFromFile(): void
    {
        $configFile = $this->tempDir . '/config.php';
        file_put_contents($configFile, '<?php return ["key" => "value"];');

        $config = new Config();
        $config->loadFromFile($configFile);

        $this->assertEquals('value', $config->get('key'));
    }

    public function testThrowsExceptionForMissingFile(): void
    {
        $config = new Config();

        $this->expectException(\InvalidArgumentException::class);
        $config->loadFromFile('/nonexistent/file.php');
    }

    public function testCanLoadEnvFile(): void
    {
        $envFile = $this->tempDir . '/.env';
        file_put_contents($envFile, "DB_HOST=localhost\nDB_PORT=3306\n");

        $config = new Config();
        $config->loadEnv($envFile);

        $this->assertEquals('localhost', $config->env('DB_HOST'));
        $this->assertEquals('3306', $config->env('DB_PORT'));
    }

    public function testCanParseEnvBooleans(): void
    {
        $envFile = $this->tempDir . '/.env';
        file_put_contents($envFile, "DEBUG=true\nPRODUCTION=false\n");

        $config = new Config();
        $config->loadEnv($envFile);

        $this->assertTrue($config->env('DEBUG'));
        $this->assertFalse($config->env('PRODUCTION'));
    }

    public function testCanParseEnvNull(): void
    {
        $envFile = $this->tempDir . '/.env';
        file_put_contents($envFile, "VALUE=null\n");

        $config = new Config();
        $config->loadEnv($envFile);

        $this->assertNull($config->env('VALUE'));
    }

    public function testCanParseQuotedEnvValues(): void
    {
        $envFile = $this->tempDir . '/.env';
        file_put_contents($envFile, "SINGLE='single quoted'\nDOUBLE=\"double quoted\"\n");

        $config = new Config();
        $config->loadEnv($envFile);

        $this->assertEquals('single quoted', $config->env('SINGLE'));
        $this->assertEquals('double quoted', $config->env('DOUBLE'));
    }

    public function testEnvCommentsAreIgnored(): void
    {
        $envFile = $this->tempDir . '/.env';
        file_put_contents($envFile, "# This is a comment\nKEY=value\n");

        $config = new Config();
        $config->loadEnv($envFile);

        $this->assertEquals('value', $config->env('KEY'));
    }

    public function testCanGetAllConfig(): void
    {
        $config = new Config(['a' => 1, 'b' => 2]);
        $all = $config->all();

        $this->assertArrayHasKey('a', $all);
        $this->assertArrayHasKey('b', $all);
    }

    public function testCanMergeConfig(): void
    {
        $config = new Config(['a' => 1]);
        $config->merge(['b' => 2]);

        $this->assertEquals(1, $config->get('a'));
        $this->assertEquals(2, $config->get('b'));
    }
}
