# PhpMigration

Migration Library for PHP

## How to use

```php
include_once __DIR__ . '/src/Database.php';
include_once __DIR__ . '/src/Utils.php';
include_once __DIR__ . '/src/Migration.php';
include_once __DIR__ . '/config.php';

use Farisc0de\PhpMigration\Database;
use Farisc0de\PhpMigration\Options\Options;
use Farisc0de\PhpMigration\Options\Types;
use Farisc0de\PhpMigration\Utils;
use Farisc0de\PhpMigration\Migration;

$obj = new Migration(new Database($config), new Utils());

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Create a new table
    $obj->createTable(
        "users",
        [
            ["id", Types::Integer(), Options::AutoIncrement(), Options::NotNull()],
            ["username", Types::String(255), Options::NotNull()],
            ["password", Types::String(255), Options::NotNull()],
            ["email", Types::String(255), Options::NotNull()],
            ["created_at", Types::TimeStamp(), Options::CurrentTimeStamp()],
            ["updated_at", Types::TimeStamp(), Options::CurrentTimeStamp()]
        ]
    );

    // Create Primary Key
    $obj->setPrimary("users", "id");

    // Add a new record
    $obj->insertValue(
        "users",
        [
            "username" => "admin",
            "password" => password_hash("admin", PASSWORD_DEFAULT),
            "email" => "admin@gmail.com",
        ]
    );

    $msg = "Database installed successfully!";

```

## Copyright

FarisCode
