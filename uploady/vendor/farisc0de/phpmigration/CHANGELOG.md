# Changelog

All notable changes to the PHP Migration Library will be documented in this file.

## [3.0.0](https://github.com/farisc0de/PhpMigration/releases/tag/v3.0.0) - 2026-01-09

### Major Features

- **Fluent Schema Builder (Blueprint API)**
  - Laravel-like fluent interface for defining tables
  - Support for all common column types
  - Column modifiers (nullable, default, unsigned, etc.)
  - Index management (primary, unique, index, fulltext, spatial)
  - Foreign key constraints with fluent API
  - Polymorphic relationship helpers (morphs)
  - Timestamps and soft deletes helpers

- **Migration Versioning System**
  - Migration tracking with batch numbers
  - Up/down migration methods for reversibility
  - Migration repository for tracking executed migrations
  - Support for step-by-step rollbacks

- **CLI Tool (`bin/migrate`)**
  - `migrate` - Run pending migrations
  - `migrate:rollback` - Rollback migrations
  - `migrate:reset` - Reset all migrations
  - `migrate:refresh` - Reset and re-run migrations
  - `migrate:status` - Show migration status
  - `migrate:install` - Create migration table
  - `make:migration` - Generate migration files
  - `make:seeder` - Generate seeder files
  - `db:seed` - Run database seeders

- **Multi-Database Support**
  - MySQL driver with full feature support
  - PostgreSQL driver with native types (SERIAL, JSONB, etc.)
  - SQLite driver for testing and lightweight apps
  - Database-agnostic schema operations

- **Database Seeding**
  - Seeder base class with helper methods
  - Seeder manager for running seeders
  - Support for calling nested seeders
  - Truncate and foreign key helpers

- **Schema Introspection**
  - Get all tables in database
  - Get column information and types
  - Get indexes and foreign keys
  - Check table/column existence
  - Get primary key columns

- **Event System**
  - PSR-compatible event dispatcher
  - Migration lifecycle events (migrating, migrated, rollingBack, rolledBack)
  - Seeder lifecycle events

- **Logging**
  - PSR-3 compatible logger
  - File and console output
  - Configurable log levels
  - Exception logging with stack traces

- **Configuration Management**
  - Support for `.env` files
  - PHP configuration files
  - Environment variable parsing
  - Dot notation for nested config

### New Classes

- `Farisc0de\PhpMigration\Schema\Blueprint`
- `Farisc0de\PhpMigration\Schema\ColumnDefinition`
- `Farisc0de\PhpMigration\Schema\ForeignKeyDefinition`
- `Farisc0de\PhpMigration\Schema\ForeignIdColumnDefinition`
- `Farisc0de\PhpMigration\Schema\SchemaBuilder`
- `Farisc0de\PhpMigration\Schema\SchemaInspector`
- `Farisc0de\PhpMigration\Schema\Grammars\Grammar`
- `Farisc0de\PhpMigration\Schema\Grammars\MySqlGrammar`
- `Farisc0de\PhpMigration\Schema\Grammars\PostgresGrammar`
- `Farisc0de\PhpMigration\Schema\Grammars\SqliteGrammar`
- `Farisc0de\PhpMigration\Migrations\Migrator`
- `Farisc0de\PhpMigration\Migrations\MigrationRepository`
- `Farisc0de\PhpMigration\Migrations\MigrationCreator`
- `Farisc0de\PhpMigration\Migrations\Migration`
- `Farisc0de\PhpMigration\Seeders\Seeder`
- `Farisc0de\PhpMigration\Seeders\SeederManager`
- `Farisc0de\PhpMigration\Seeders\SeederCreator`
- `Farisc0de\PhpMigration\Database\Connection`
- `Farisc0de\PhpMigration\Database\ConnectionFactory`
- `Farisc0de\PhpMigration\Console\Application`
- `Farisc0de\PhpMigration\Console\Command`
- `Farisc0de\PhpMigration\Console\Commands\*`
- `Farisc0de\PhpMigration\Support\Config`
- `Farisc0de\PhpMigration\Support\EventDispatcher`
- `Farisc0de\PhpMigration\Support\Logger`
- `Farisc0de\PhpMigration\Contracts\*`

### Breaking Changes

- Minimum PHP version increased to 8.1
- New `ConnectionInterface` for database connections
- New `MigrationInterface` for migration classes
- New `SchemaGrammarInterface` for database grammars

### Backward Compatibility

- Original `Database`, `Migration`, `Utils`, `Options`, and `Types` classes preserved
- Legacy API continues to work for existing projects

---

## [2.0.0](https://github.com/farisc0de/PhpMigration/releases/tag/v2.0.0) - 2025-01-14

### Breaking Changes
- Added strict type hints across all classes
- Changed method signatures to require proper types
- Renamed several methods for better clarity:
  - `setAutoinc()` → `setAutoIncrement()`
  - `createColumn()` → `addColumn()`
  - `updateColumnType()` → `modifyColumn()`
  - `unSigned()` → `unsigned()`

### Added
- **Database Class**
  - Added proper error handling with PDOException
  - Added configurable charset support
  - Added configurable fetch style
  - Added input validation for database configuration
  - Added proper type declarations for properties

- **Migration Class**
  - Added SQL constants for better maintainability
  - Added support for multiple columns in constraints
  - Added validation for foreign key actions
  - Added support for named indexes
  - Added comprehensive error handling

- **Options Class**
  - Added constants for common SQL values
  - Added support for multiple columns in constraints
  - Added validation for foreign key actions
  - Added support for named indexes
  - Added enum support for ON DELETE and ON UPDATE actions

- **Types Class**
  - Added constants for all MySQL data types
  - Added new data type methods:
    - `decimal()`
    - `float()`
    - `double()`
    - `date()`
    - `time()`
    - `year()`
    - `char()`
    - `enum()`
  - Added input validation for numeric parameters

- **Utils Class**
  - Added new utility methods:
    - `escapeString()`
    - `formatTimestamp()`
    - `toSqlValue()`
    - `generateIndexName()`
    - `isValidIdentifier()`
  - Added MySQL identifier length validation
  - Added proper SQL injection prevention
  - Added timestamp formatting support

### Improved
- Better type safety with PHP 7.4+ features
- More comprehensive error handling
- Better code organization and maintainability
- More consistent naming conventions
- Better documentation with detailed PHPDoc blocks
- Improved security with proper input validation
- Better SQL string formatting using sprintf

### Fixed
- Fixed potential SQL injection vulnerabilities
- Fixed improper error handling in database connections
- Fixed inconsistent return types
- Fixed missing input validation
- Fixed potential issues with identifier lengths

## Migration Guide

### Upgrading from 1.x to 2.0.0

1. Database Configuration
```php
// Old
$config = [];
$db = new Database($config); // Would work but not safe

// New
$config = [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'username',
    'DB_PASS' => 'password',
    'DB_NAME' => 'database',
    'DB_CHARSET' => 'utf8mb4', // optional
    'FETCH_STYLE' => PDO::FETCH_ASSOC // optional
];
$db = new Database($config);
```

2. Method Name Changes
```php
// Old
$migration->setAutoinc($table, $column);
$migration->createColumn($table, $column);
$migration->updateColumnType($table, $column);

// New
$migration->setAutoIncrement($table, $column);
$migration->addColumn($table, $column);
$migration->modifyColumn($table, $column);
```

3. Foreign Key Definition
```php
// Old
$migration->foreignKey('user_id', ['users' => 'id']);

// New
$migration->foreignKey(
    'user_id',
    'users',
    'id',
    Options::CASCADE, // ON DELETE
    Options::CASCADE  // ON UPDATE
);
```

4. Utils Usage
```php
// Old
$utils->sanitize($value); // Basic sanitization

// New
$utils->sanitize($identifier); // For database identifiers
$utils->escapeString($value); // For string values
$utils->toSqlValue($value);   // For any SQL value
```
