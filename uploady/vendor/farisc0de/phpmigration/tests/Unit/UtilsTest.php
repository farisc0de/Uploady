<?php

namespace Farisc0de\PhpMigration\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpMigration\Utils;

class UtilsTest extends TestCase
{
    protected Utils $utils;

    protected function setUp(): void
    {
        $this->utils = new Utils();
    }

    public function testCanSanitizeIdentifier(): void
    {
        $this->assertEquals('users', $this->utils->sanitize('users'));
        $this->assertEquals('user_table', $this->utils->sanitize('user_table'));
    }

    public function testSanitizeRemovesUnsafeCharacters(): void
    {
        $this->assertEquals('users', $this->utils->sanitize('users;'));
        $this->assertEquals('userstable', $this->utils->sanitize('users-table'));
        $this->assertEquals('userstable', $this->utils->sanitize('users table'));
    }

    public function testSanitizeThrowsExceptionForEmptyIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->utils->sanitize('');
    }

    public function testSanitizeThrowsExceptionForTooLongIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->utils->sanitize(str_repeat('a', 65));
    }

    public function testCanEscapeString(): void
    {
        $this->assertEquals("'hello'", $this->utils->escapeString('hello'));
        $this->assertEquals("'it\\'s'", $this->utils->escapeString("it's"));
    }

    public function testEscapeStringHandlesNull(): void
    {
        $this->assertEquals('NULL', $this->utils->escapeString(null));
    }

    public function testCanFormatTimestamp(): void
    {
        $timestamp = strtotime('2024-01-15 10:30:00');
        $this->assertEquals('2024-01-15 10:30:00', $this->utils->formatTimestamp($timestamp));
    }

    public function testFormatTimestampHandlesDateTimeInterface(): void
    {
        $datetime = new \DateTime('2024-01-15 10:30:00');
        $this->assertEquals('2024-01-15 10:30:00', $this->utils->formatTimestamp($datetime));
    }

    public function testFormatTimestampHandlesString(): void
    {
        $this->assertEquals('2024-01-15 10:30:00', $this->utils->formatTimestamp('2024-01-15 10:30:00'));
    }

    public function testFormatTimestampHandlesNull(): void
    {
        $this->assertEquals('NULL', $this->utils->formatTimestamp(null));
    }

    public function testCanConvertToSqlValue(): void
    {
        $this->assertEquals('NULL', $this->utils->toSqlValue(null));
        $this->assertEquals('1', $this->utils->toSqlValue(true));
        $this->assertEquals('0', $this->utils->toSqlValue(false));
        $this->assertEquals('42', $this->utils->toSqlValue(42));
        $this->assertEquals('3.14', $this->utils->toSqlValue(3.14));
        $this->assertEquals("'hello'", $this->utils->toSqlValue('hello'));
    }

    public function testCanGenerateIndexName(): void
    {
        $name = $this->utils->generateIndexName('users', 'email', 'idx');
        $this->assertEquals('idx_users_email', $name);
    }

    public function testCanGenerateIndexNameWithMultipleColumns(): void
    {
        $name = $this->utils->generateIndexName('users', ['first_name', 'last_name'], 'idx');
        $this->assertEquals('idx_users_first_name_last_name', $name);
    }

    public function testGenerateIndexNameTruncatesLongNames(): void
    {
        $longTable = str_repeat('a', 50);
        $longColumn = str_repeat('b', 50);
        
        $name = $this->utils->generateIndexName($longTable, $longColumn, 'idx');
        
        $this->assertLessThanOrEqual(64, strlen($name));
    }

    public function testCanValidateIdentifier(): void
    {
        $this->assertTrue($this->utils->isValidIdentifier('users'));
        $this->assertTrue($this->utils->isValidIdentifier('user_table'));
        $this->assertTrue($this->utils->isValidIdentifier('_private'));
        
        $this->assertFalse($this->utils->isValidIdentifier(''));
        $this->assertFalse($this->utils->isValidIdentifier('123users'));
        $this->assertFalse($this->utils->isValidIdentifier('user-table'));
        $this->assertFalse($this->utils->isValidIdentifier(str_repeat('a', 65)));
    }
}
