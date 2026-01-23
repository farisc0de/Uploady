<?php

namespace Farisc0de\PhpMigration\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Database\Connection;
use Farisc0de\PhpMigration\Migrations\Migrator;
use Farisc0de\PhpMigration\Migrations\MigrationRepository;
use Farisc0de\PhpMigration\Migrations\MigrationCreator;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Grammars\SqliteGrammar;
use PDO;

class MigrationIntegrationTest extends TestCase
{
    protected Connection $connection;
    protected Migrator $migrator;
    protected MigrationRepository $repository;
    protected string $migrationsPath;

    protected function setUp(): void
    {
        // Create in-memory SQLite connection
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->connection = new Connection($pdo, ':memory:', 'sqlite');
        
        $grammar = new SqliteGrammar();
        $this->repository = new MigrationRepository($this->connection);
        $this->migrator = new Migrator($this->repository, $this->connection, $grammar);

        // Create temp migrations directory
        $this->migrationsPath = sys_get_temp_dir() . '/phpmigration_integration_' . uniqid();
        mkdir($this->migrationsPath, 0755, true);
        $this->migrator->path($this->migrationsPath);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->migrationsPath);
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

    protected function createMigrationFile(string $name, string $content): string
    {
        $filename = date('Y_m_d_His') . '_' . $name . '.php';
        $path = $this->migrationsPath . '/' . $filename;
        file_put_contents($path, $content);
        usleep(1000); // Ensure unique timestamps
        return $path;
    }

    public function testCanCreateMigrationRepository(): void
    {
        $this->repository->createRepository();
        $this->assertTrue($this->repository->repositoryExists());
    }

    public function testCanRunMigration(): void
    {
        $this->createMigrationFile('create_users_table', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255);
        });
    }
    
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('users');
    }
};
PHP
        );

        $migrations = $this->migrator->run();

        $this->assertCount(1, $migrations);
        $this->assertTrue($this->tableExists('users'));
    }

    public function testCanRollbackMigration(): void
    {
        $this->createMigrationFile('create_posts_table', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
        });
    }
    
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('posts');
    }
};
PHP
        );

        $this->migrator->run();
        $this->assertTrue($this->tableExists('posts'));

        $this->migrator->rollback();
        $this->assertFalse($this->tableExists('posts'));
    }

    public function testCanGetMigrationStatus(): void
    {
        $this->createMigrationFile('create_comments_table', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {}
    public function down(SchemaBuilder $schema): void {}
};
PHP
        );

        $status = $this->migrator->status();

        $this->assertCount(1, $status);
        $this->assertEquals('Pending', $status[0]['status']);

        $this->migrator->run();
        $status = $this->migrator->status();

        $this->assertEquals('Ran', $status[0]['status']);
    }

    public function testCanResetMigrations(): void
    {
        $this->createMigrationFile('create_tags_table', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });
    }
    
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('tags');
    }
};
PHP
        );

        $this->migrator->run();
        $this->assertTrue($this->tableExists('tags'));

        $this->migrator->reset();
        $this->assertFalse($this->tableExists('tags'));
    }

    public function testCanRefreshMigrations(): void
    {
        $this->createMigrationFile('create_categories_table', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });
    }
    
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('categories');
    }
};
PHP
        );

        $this->migrator->run();
        $migrations = $this->migrator->refresh();

        $this->assertNotEmpty($migrations);
        $this->assertTrue($this->tableExists('categories'));
    }

    public function testMigrationsRunInOrder(): void
    {
        $this->createMigrationFile('001_first', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('first_table', function (Blueprint $table) {
            $table->id();
        });
    }
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('first_table');
    }
};
PHP
        );

        usleep(10000);

        $this->createMigrationFile('002_second', <<<'PHP'
<?php
use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface {
    public function up(SchemaBuilder $schema): void {
        $schema->create('second_table', function (Blueprint $table) {
            $table->id();
        });
    }
    public function down(SchemaBuilder $schema): void {
        $schema->dropIfExists('second_table');
    }
};
PHP
        );

        $migrations = $this->migrator->run();

        $this->assertCount(2, $migrations);
        $this->assertTrue($this->tableExists('first_table'));
        $this->assertTrue($this->tableExists('second_table'));
    }

    protected function tableExists(string $table): bool
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=:table";
        $this->connection->prepare($sql);
        $this->connection->bind(':table', $table);
        $this->connection->execute();
        return $this->connection->rowCount() > 0;
    }
}
