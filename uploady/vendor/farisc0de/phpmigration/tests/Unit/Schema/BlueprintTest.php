<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Schema\Blueprint;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

class BlueprintTest extends TestCase
{
    public function testCanCreateBlueprint(): void
    {
        $blueprint = new Blueprint('users');
        $this->assertEquals('users', $blueprint->getTable());
    }

    public function testCanAddIdColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->id();

        $this->assertInstanceOf(ColumnDefinition::class, $column);
        $this->assertEquals('id', $column->get('name'));
        $this->assertEquals('bigInteger', $column->get('type'));
        $this->assertTrue($column->get('autoIncrement'));
        $this->assertTrue($column->get('unsigned'));
    }

    public function testCanAddStringColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->string('email', 100);

        $this->assertEquals('email', $column->get('name'));
        $this->assertEquals('string', $column->get('type'));
        $this->assertEquals(100, $column->get('length'));
    }

    public function testCanAddIntegerColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->integer('age');

        $this->assertEquals('age', $column->get('name'));
        $this->assertEquals('integer', $column->get('type'));
    }

    public function testCanAddTextColumn(): void
    {
        $blueprint = new Blueprint('posts');
        $column = $blueprint->text('content');

        $this->assertEquals('content', $column->get('name'));
        $this->assertEquals('text', $column->get('type'));
    }

    public function testCanAddBooleanColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->boolean('is_active');

        $this->assertEquals('is_active', $column->get('name'));
        $this->assertEquals('boolean', $column->get('type'));
    }

    public function testCanAddTimestamps(): void
    {
        $blueprint = new Blueprint('users');
        $blueprint->timestamps();

        $columns = $blueprint->getColumns();
        $this->assertCount(2, $columns);

        $columnNames = array_map(fn($col) => $col->get('name'), $columns);
        $this->assertContains('created_at', $columnNames);
        $this->assertContains('updated_at', $columnNames);
    }

    public function testCanAddSoftDeletes(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->softDeletes();

        $this->assertEquals('deleted_at', $column->get('name'));
        $this->assertEquals('timestamp', $column->get('type'));
        $this->assertTrue($column->get('nullable'));
    }

    public function testCanAddForeignId(): void
    {
        $blueprint = new Blueprint('posts');
        $foreignId = $blueprint->foreignId('user_id');

        $columns = $blueprint->getColumns();
        $this->assertCount(1, $columns);
        $this->assertEquals('user_id', $columns[0]->get('name'));
    }

    public function testCanAddUniqueIndex(): void
    {
        $blueprint = new Blueprint('users');
        $blueprint->unique('email');

        $commands = $blueprint->getCommands();
        $this->assertCount(1, $commands);
        $this->assertEquals('unique', $commands[0]['name']);
    }

    public function testCanAddIndex(): void
    {
        $blueprint = new Blueprint('users');
        $blueprint->index(['first_name', 'last_name']);

        $commands = $blueprint->getCommands();
        $this->assertCount(1, $commands);
        $this->assertEquals('index', $commands[0]['name']);
    }

    public function testCanAddForeignKey(): void
    {
        $blueprint = new Blueprint('posts');
        $blueprint->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('CASCADE');

        $commands = $blueprint->getCommands();
        $this->assertCount(1, $commands);
        $this->assertEquals('foreign', $commands[0]['name']);
        $this->assertEquals('id', $commands[0]['references']);
        $this->assertEquals('users', $commands[0]['on']);
        $this->assertEquals('CASCADE', $commands[0]['onDelete']);
    }

    public function testCanSetTableEngine(): void
    {
        $blueprint = new Blueprint('users');
        $blueprint->engine('InnoDB');

        $this->assertEquals('InnoDB', $blueprint->engine);
    }

    public function testCanSetTableCharset(): void
    {
        $blueprint = new Blueprint('users');
        $blueprint->charset('utf8mb4');

        $this->assertEquals('utf8mb4', $blueprint->charset);
    }

    public function testCanAddEnumColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->enum('status', ['active', 'inactive', 'pending']);

        $this->assertEquals('status', $column->get('name'));
        $this->assertEquals('enum', $column->get('type'));
        $this->assertEquals(['active', 'inactive', 'pending'], $column->get('allowed'));
    }

    public function testCanAddJsonColumn(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->json('metadata');

        $this->assertEquals('metadata', $column->get('name'));
        $this->assertEquals('json', $column->get('type'));
    }

    public function testCanAddDecimalColumn(): void
    {
        $blueprint = new Blueprint('products');
        $column = $blueprint->decimal('price', 10, 2);

        $this->assertEquals('price', $column->get('name'));
        $this->assertEquals('decimal', $column->get('type'));
        $this->assertEquals(10, $column->get('precision'));
        $this->assertEquals(2, $column->get('scale'));
    }

    public function testCanAddMorphsColumns(): void
    {
        $blueprint = new Blueprint('comments');
        $blueprint->morphs('commentable');

        $columns = $blueprint->getColumns();
        $this->assertCount(2, $columns);

        $columnNames = array_map(fn($col) => $col->get('name'), $columns);
        $this->assertContains('commentable_id', $columnNames);
        $this->assertContains('commentable_type', $columnNames);
    }

    public function testCanAddRememberToken(): void
    {
        $blueprint = new Blueprint('users');
        $column = $blueprint->rememberToken();

        $this->assertEquals('remember_token', $column->get('name'));
        $this->assertEquals('string', $column->get('type'));
        $this->assertEquals(100, $column->get('length'));
        $this->assertTrue($column->get('nullable'));
    }
}
