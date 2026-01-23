# PhpMigration

Enterprise-ready database migration library for PHP with fluent schema builder, versioning, CLI tools, and multi-database support.

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Features

- **Fluent Schema Builder** - Laravel-like Blueprint API for defining tables
- **Migration Versioning** - Track and manage migration history with batches
- **CLI Tools** - Full command-line interface for all migration operations
- **Multi-Database Support** - MySQL, PostgreSQL, and SQLite drivers
- **Database Seeding** - Populate your database with test or initial data
- **Schema Introspection** - Inspect existing database structure
- **Event System** - Hook into migration lifecycle events
- **PSR-3 Logging** - Compatible logging implementation
- **Environment Configuration** - Support for `.env` files

## Requirements

- PHP 8.1 or higher
- PDO extension
- One of: pdo_mysql, pdo_pgsql, or pdo_sqlite

## Installation

```bash
composer require farisc0de/phpmigration
```

## Quick Start

### 1. Create Configuration

Create a `.env` file in your project root:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Or create `config/database.php`:

```php
<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'your_database',
            'username' => 'your_username',
            'password' => 'your_password',
            'charset' => 'utf8mb4',
        ],
    ],
];
```

### 2. Create a Migration

```bash
./vendor/bin/migrate make:migration create_users_table --create=users
```

This creates a file in `database/migrations/`:

```php
<?php

use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropIfExists('users');
    }
};
```

### 3. Run Migrations

```bash
./vendor/bin/migrate migrate
```

## CLI Commands

| Command | Description |
|---------|-------------|
| `migrate` | Run all pending migrations |
| `migrate:rollback` | Rollback the last batch of migrations |
| `migrate:reset` | Rollback all migrations |
| `migrate:refresh` | Reset and re-run all migrations |
| `migrate:status` | Show the status of each migration |
| `migrate:install` | Create the migration repository table |
| `make:migration` | Create a new migration file |
| `make:seeder` | Create a new seeder class |
| `db:seed` | Run database seeders |

### Command Options

```bash
# Run migrations with step mode (each migration in its own batch)
./vendor/bin/migrate migrate --step

# Rollback last 3 batches
./vendor/bin/migrate migrate:rollback --step=3

# Create migration for existing table
./vendor/bin/migrate make:migration add_phone_to_users --table=users

# Specify custom path
./vendor/bin/migrate migrate --path=/custom/migrations/path
```

## Schema Builder API

### Column Types

```php
$table->id();                          // Auto-incrementing BIGINT primary key
$table->bigIncrements('id');           // Auto-incrementing BIGINT
$table->increments('id');              // Auto-incrementing INT

$table->string('name', 100);           // VARCHAR(100)
$table->char('code', 4);               // CHAR(4)
$table->text('description');           // TEXT
$table->mediumText('content');         // MEDIUMTEXT
$table->longText('body');              // LONGTEXT

$table->integer('votes');              // INT
$table->tinyInteger('level');          // TINYINT
$table->smallInteger('rank');          // SMALLINT
$table->mediumInteger('score');        // MEDIUMINT
$table->bigInteger('views');           // BIGINT
$table->unsignedInteger('count');      // UNSIGNED INT

$table->float('amount', 8, 2);         // FLOAT(8,2)
$table->double('price', 15, 8);        // DOUBLE(15,8)
$table->decimal('total', 10, 2);       // DECIMAL(10,2)

$table->boolean('active');             // TINYINT(1)

$table->date('birth_date');            // DATE
$table->dateTime('published_at');      // DATETIME
$table->time('alarm_time');            // TIME
$table->timestamp('added_at');         // TIMESTAMP
$table->year('graduation_year');       // YEAR

$table->binary('data');                // BLOB
$table->json('options');               // JSON

$table->enum('status', ['draft', 'published']);  // ENUM
$table->uuid('uuid');                  // CHAR(36)
```

### Column Modifiers

```php
$table->string('email')->nullable();           // Allow NULL
$table->string('name')->default('Guest');      // Default value
$table->integer('votes')->unsigned();          // UNSIGNED
$table->string('email')->unique();             // UNIQUE constraint
$table->integer('id')->primary();              // PRIMARY KEY
$table->string('bio')->comment('User bio');    // Column comment
$table->string('phone')->after('email');       // Position after column (MySQL)
$table->string('id')->first();                 // Position first (MySQL)
$table->timestamp('created_at')->useCurrent(); // DEFAULT CURRENT_TIMESTAMP
$table->timestamp('updated_at')->useCurrentOnUpdate(); // ON UPDATE CURRENT_TIMESTAMP
```

### Indexes

```php
$table->primary('id');                         // Primary key
$table->primary(['first', 'last']);            // Composite primary key
$table->unique('email');                       // Unique index
$table->index('state');                        // Basic index
$table->index(['account_id', 'created_at']);   // Composite index
$table->fullText('body');                      // Full-text index (MySQL)
$table->spatialIndex('location');              // Spatial index (MySQL)
```

### Foreign Keys

```php
// Simple foreign key
$table->foreignId('user_id')->constrained();

// With options
$table->foreignId('user_id')
    ->constrained('users', 'id')
    ->onDelete('CASCADE')
    ->onUpdate('CASCADE');

// Manual foreign key
$table->foreign('user_id')
    ->references('id')
    ->on('users')
    ->onDelete('CASCADE');
```

### Timestamps & Soft Deletes

```php
$table->timestamps();              // created_at and updated_at
$table->softDeletes();             // deleted_at for soft deletes
$table->rememberToken();           // remember_token for auth
```

### Polymorphic Relationships

```php
$table->morphs('taggable');        // taggable_id and taggable_type
$table->nullableMorphs('taggable'); // Nullable morphs
$table->uuidMorphs('taggable');    // UUID morphs
```

## Database Seeding

### Create a Seeder

```bash
./vendor/bin/migrate make:seeder UsersSeeder
```

```php
<?php

namespace Database\Seeders;

use Farisc0de\PhpMigration\Seeders\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->insert('users', [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => password_hash('secret', PASSWORD_DEFAULT),
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => password_hash('secret', PASSWORD_DEFAULT),
            ],
        ]);

        // Call other seeders
        $this->call(PostsSeeder::class);
    }
}
```

### Run Seeders

```bash
./vendor/bin/migrate db:seed
./vendor/bin/migrate db:seed UsersSeeder
```

## Programmatic Usage

```php
<?php

use Farisc0de\PhpMigration\Database\Connection;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Grammars\MySqlGrammar;
use Farisc0de\PhpMigration\Migrations\Migrator;
use Farisc0de\PhpMigration\Migrations\MigrationRepository;

// Create connection
$connection = Connection::create([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'myapp',
    'username' => 'root',
    'password' => '',
]);

// Create schema builder
$grammar = new MySqlGrammar();
$schema = new SchemaBuilder($connection, $grammar);

// Create table
$schema->create('posts', function ($table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->foreignId('user_id')->constrained();
    $table->timestamps();
});

// Check if table exists
if ($schema->hasTable('posts')) {
    // Modify table
    $schema->table('posts', function ($table) {
        $table->string('slug')->after('title');
        $table->index('slug');
    });
}

// Drop table
$schema->dropIfExists('posts');
```

## Schema Introspection

```php
use Farisc0de\PhpMigration\Schema\SchemaInspector;

$inspector = new SchemaInspector($connection);

// Get all tables
$tables = $inspector->getTables();

// Check table/column existence
$inspector->hasTable('users');
$inspector->hasColumn('users', 'email');

// Get column information
$columns = $inspector->getColumns('users');
$type = $inspector->getColumnType('users', 'email');

// Get indexes and foreign keys
$indexes = $inspector->getIndexes('users');
$foreignKeys = $inspector->getForeignKeys('posts');

// Get primary key
$primaryKey = $inspector->getPrimaryKey('users');

// Get table details
$details = $inspector->getTableDetails('users');
```

## Events

```php
use Farisc0de\PhpMigration\Support\EventDispatcher;

$events = new EventDispatcher();

$events->listen('migration.migrating', function ($payload) {
    echo "Running: {$payload['migration']}\n";
});

$events->listen('migration.migrated', function ($payload) {
    echo "Completed: {$payload['migration']}\n";
});

// Pass to migrator
$migrator = new Migrator($repository, $connection, $grammar, $events);
```

## Logging

```php
use Farisc0de\PhpMigration\Support\Logger;

$logger = new Logger(
    logFile: 'logs/migrations.log',
    minLevel: 'info',
    console: true
);

$logger->info('Migration started');
$logger->error('Migration failed', ['exception' => $e]);
```

## Legacy API (v1.x Compatibility)

The original API is still available for backward compatibility:

```php
use Farisc0de\PhpMigration\Database;
use Farisc0de\PhpMigration\Migration;
use Farisc0de\PhpMigration\Utils;
use Farisc0de\PhpMigration\Options\Options;
use Farisc0de\PhpMigration\Options\Types;

$db = new Database([
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'myapp',
]);

$migration = new Migration($db, new Utils());

$migration->createTable('users', [
    ['id', Types::integer(), Options::autoIncrement(), Options::notNull()],
    ['email', Types::string(255), Options::notNull()],
    ['created_at', Types::timestamp(), Options::currentTimeStamp()],
]);

$migration->setPrimary('users', 'id');
$migration->setUnique('users', 'email');
```

## Testing

```bash
composer test
composer test-coverage
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Faris AL-Otaibi** - [FarisCode](https://github.com/farisc0de)
