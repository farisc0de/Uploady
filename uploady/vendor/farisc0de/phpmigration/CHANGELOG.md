# Changelog

All notable changes to the PHP Migration Library will be documented in this file.

## [2.0.0] - 2025-01-14

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
