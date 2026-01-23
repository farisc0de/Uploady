<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\Grammars\MySqlGrammar;
use Farisc0de\PhpMigration\Schema\Grammars\PostgresGrammar;
use Farisc0de\PhpMigration\Schema\Grammars\SqliteGrammar;

class GrammarTest extends TestCase
{
    public function testMySqlGrammarCompileCreate(): void
    {
        $grammar = new MySqlGrammar();
        $blueprint = new Blueprint('users');
        $blueprint->id();
        $blueprint->string('email', 255);
        $blueprint->timestamps();

        $statements = $grammar->compileCreate($blueprint);

        $this->assertNotEmpty($statements);
        $this->assertStringContainsString('CREATE TABLE', $statements[0]);
        $this->assertStringContainsString('`users`', $statements[0]);
        $this->assertStringContainsString('`id`', $statements[0]);
        $this->assertStringContainsString('`email`', $statements[0]);
    }

    public function testMySqlGrammarCompileDrop(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileDrop('users');

        $this->assertEquals('DROP TABLE `users`', $sql);
    }

    public function testMySqlGrammarCompileDropIfExists(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileDropIfExists('users');

        $this->assertEquals('DROP TABLE IF EXISTS `users`', $sql);
    }

    public function testMySqlGrammarCompileRename(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileRename('users', 'members');

        $this->assertEquals('RENAME TABLE `users` TO `members`', $sql);
    }

    public function testPostgresGrammarCompileCreate(): void
    {
        $grammar = new PostgresGrammar();
        $blueprint = new Blueprint('users');
        $blueprint->id();
        $blueprint->string('email', 255);

        $statements = $grammar->compileCreate($blueprint);

        $this->assertNotEmpty($statements);
        $this->assertStringContainsString('CREATE TABLE', $statements[0]);
        $this->assertStringContainsString('"users"', $statements[0]);
        $this->assertStringContainsString('"id"', $statements[0]);
    }

    public function testPostgresGrammarUsesSerial(): void
    {
        $grammar = new PostgresGrammar();
        $blueprint = new Blueprint('users');
        $blueprint->id();

        $statements = $grammar->compileCreate($blueprint);

        $this->assertStringContainsString('BIGSERIAL', $statements[0]);
    }

    public function testSqliteGrammarCompileCreate(): void
    {
        $grammar = new SqliteGrammar();
        $blueprint = new Blueprint('users');
        $blueprint->id();
        $blueprint->string('email', 255);

        $statements = $grammar->compileCreate($blueprint);

        $this->assertNotEmpty($statements);
        $this->assertStringContainsString('CREATE TABLE', $statements[0]);
        $this->assertStringContainsString('"users"', $statements[0]);
    }

    public function testSqliteGrammarUsesAutoincrement(): void
    {
        $grammar = new SqliteGrammar();
        $blueprint = new Blueprint('users');
        $blueprint->id();

        $statements = $grammar->compileCreate($blueprint);

        $this->assertStringContainsString('AUTOINCREMENT', $statements[0]);
    }

    public function testMySqlGrammarTablePrefix(): void
    {
        $grammar = new MySqlGrammar();
        $grammar->setTablePrefix('app_');

        $sql = $grammar->compileDrop('users');

        $this->assertEquals('DROP TABLE `app_users`', $sql);
    }

    public function testMySqlGrammarCompileTableExists(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileTableExists();

        $this->assertStringContainsString('information_schema.tables', $sql);
        $this->assertStringContainsString(':database', $sql);
        $this->assertStringContainsString(':table', $sql);
    }

    public function testMySqlGrammarCompileColumnListing(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileColumnListing();

        $this->assertStringContainsString('information_schema.columns', $sql);
        $this->assertStringContainsString('column_name', $sql);
    }

    public function testMySqlGrammarDriverName(): void
    {
        $grammar = new MySqlGrammar();
        $this->assertEquals('mysql', $grammar->getDriverName());
    }

    public function testPostgresGrammarDriverName(): void
    {
        $grammar = new PostgresGrammar();
        $this->assertEquals('pgsql', $grammar->getDriverName());
    }

    public function testSqliteGrammarDriverName(): void
    {
        $grammar = new SqliteGrammar();
        $this->assertEquals('sqlite', $grammar->getDriverName());
    }
}
