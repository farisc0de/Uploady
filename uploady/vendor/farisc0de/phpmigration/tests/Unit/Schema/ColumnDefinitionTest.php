<?php

namespace Farisc0de\PhpMigration\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Schema\ColumnDefinition;

class ColumnDefinitionTest extends TestCase
{
    public function testCanCreateColumnDefinition(): void
    {
        $column = new ColumnDefinition([
            'type' => 'string',
            'name' => 'email',
            'length' => 255,
        ]);

        $this->assertEquals('string', $column->get('type'));
        $this->assertEquals('email', $column->get('name'));
        $this->assertEquals(255, $column->get('length'));
    }

    public function testCanSetNullable(): void
    {
        $column = new ColumnDefinition(['name' => 'bio']);
        $column->nullable();

        $this->assertTrue($column->get('nullable'));
    }

    public function testCanSetDefault(): void
    {
        $column = new ColumnDefinition(['name' => 'status']);
        $column->default('active');

        $this->assertEquals('active', $column->get('default'));
    }

    public function testCanSetUnsigned(): void
    {
        $column = new ColumnDefinition(['name' => 'age']);
        $column->unsigned();

        $this->assertTrue($column->get('unsigned'));
    }

    public function testCanSetAutoIncrement(): void
    {
        $column = new ColumnDefinition(['name' => 'id']);
        $column->autoIncrement();

        $this->assertTrue($column->get('autoIncrement'));
    }

    public function testCanSetPrimary(): void
    {
        $column = new ColumnDefinition(['name' => 'id']);
        $column->primary();

        $this->assertTrue($column->get('primary'));
    }

    public function testCanSetUnique(): void
    {
        $column = new ColumnDefinition(['name' => 'email']);
        $column->unique();

        $this->assertTrue($column->get('unique'));
    }

    public function testCanSetComment(): void
    {
        $column = new ColumnDefinition(['name' => 'email']);
        $column->comment('User email address');

        $this->assertEquals('User email address', $column->get('comment'));
    }

    public function testCanSetAfter(): void
    {
        $column = new ColumnDefinition(['name' => 'phone']);
        $column->after('email');

        $this->assertEquals('email', $column->get('after'));
    }

    public function testCanSetFirst(): void
    {
        $column = new ColumnDefinition(['name' => 'id']);
        $column->first();

        $this->assertTrue($column->get('first'));
    }

    public function testCanSetUseCurrent(): void
    {
        $column = new ColumnDefinition(['name' => 'created_at']);
        $column->useCurrent();

        $this->assertTrue($column->get('useCurrent'));
    }

    public function testCanSetUseCurrentOnUpdate(): void
    {
        $column = new ColumnDefinition(['name' => 'updated_at']);
        $column->useCurrentOnUpdate();

        $this->assertTrue($column->get('useCurrentOnUpdate'));
    }

    public function testCanChainModifiers(): void
    {
        $column = new ColumnDefinition(['name' => 'email', 'type' => 'string']);
        $column->nullable()->default(null)->unique()->comment('Email');

        $this->assertTrue($column->get('nullable'));
        $this->assertNull($column->get('default'));
        $this->assertTrue($column->get('unique'));
        $this->assertEquals('Email', $column->get('comment'));
    }

    public function testCanSetCharset(): void
    {
        $column = new ColumnDefinition(['name' => 'name']);
        $column->charset('utf8mb4');

        $this->assertEquals('utf8mb4', $column->get('charset'));
    }

    public function testCanSetCollation(): void
    {
        $column = new ColumnDefinition(['name' => 'name']);
        $column->collation('utf8mb4_unicode_ci');

        $this->assertEquals('utf8mb4_unicode_ci', $column->get('collation'));
    }

    public function testCanSetChange(): void
    {
        $column = new ColumnDefinition(['name' => 'email']);
        $column->change();

        $this->assertTrue($column->get('change'));
    }

    public function testCanGetAllAttributes(): void
    {
        $column = new ColumnDefinition([
            'name' => 'email',
            'type' => 'string',
            'length' => 255,
        ]);
        $column->nullable()->unique();

        $attributes = $column->getAttributes();

        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('type', $attributes);
        $this->assertArrayHasKey('length', $attributes);
        $this->assertArrayHasKey('nullable', $attributes);
        $this->assertArrayHasKey('unique', $attributes);
    }
}
