<?php

namespace Farisc0de\PhpMigration\Migrations;

use InvalidArgumentException;

/**
 * Class MigrationCreator
 * 
 * Creates new migration files from stubs
 */
class MigrationCreator
{
    /**
     * The path to the stubs directory
     *
     * @var string
     */
    protected string $stubPath;

    /**
     * Create a new migration creator instance
     *
     * @param string|null $stubPath
     */
    public function __construct(?string $stubPath = null)
    {
        $this->stubPath = $stubPath ?? dirname(__DIR__, 2) . '/stubs';
    }

    /**
     * Create a new migration file
     *
     * @param string $name The name of the migration
     * @param string $path The path where the migration should be created
     * @param string|null $table The table to migrate
     * @param bool $create Whether this is a create table migration
     * @return string The path to the created migration file
     */
    public function create(string $name, string $path, ?string $table = null, bool $create = false): string
    {
        $this->ensureMigrationDoesntAlreadyExist($name, $path);

        $stub = $this->getStub($table, $create);

        $filename = $this->getDatePrefix() . '_' . $name . '.php';
        $filepath = $path . '/' . $filename;

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $content = $this->populateStub($stub, $name, $table);

        file_put_contents($filepath, $content);

        return $filepath;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist
     *
     * @param string $name
     * @param string $path
     * @return void
     * @throws InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist(string $name, string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);

        foreach ($files as $file) {
            if (str_contains($file, $name . '.php')) {
                throw new InvalidArgumentException("A migration with the name '{$name}' already exists.");
            }
        }
    }

    /**
     * Get the migration stub file
     *
     * @param string|null $table
     * @param bool $create
     * @return string
     */
    protected function getStub(?string $table, bool $create): string
    {
        if ($table === null) {
            $stubFile = $this->stubPath . '/migration.stub';
        } elseif ($create) {
            $stubFile = $this->stubPath . '/migration.create.stub';
        } else {
            $stubFile = $this->stubPath . '/migration.update.stub';
        }

        if (!file_exists($stubFile)) {
            return $this->getDefaultStub($table, $create);
        }

        return file_get_contents($stubFile);
    }

    /**
     * Get the default stub content
     *
     * @param string|null $table
     * @param bool $create
     * @return string
     */
    protected function getDefaultStub(?string $table, bool $create): string
    {
        if ($table === null) {
            return <<<'STUB'
<?php

use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;

return new class implements MigrationInterface
{
    /**
     * Run the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function up(SchemaBuilder $schema): void
    {
        //
    }

    /**
     * Reverse the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function down(SchemaBuilder $schema): void
    {
        //
    }
};
STUB;
        }

        if ($create) {
            return <<<'STUB'
<?php

use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface
{
    /**
     * Run the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function up(SchemaBuilder $schema): void
    {
        $schema->create('{{ table }}', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function down(SchemaBuilder $schema): void
    {
        $schema->dropIfExists('{{ table }}');
    }
};
STUB;
        }

        return <<<'STUB'
<?php

use Farisc0de\PhpMigration\Contracts\MigrationInterface;
use Farisc0de\PhpMigration\Schema\SchemaBuilder;
use Farisc0de\PhpMigration\Schema\Blueprint;

return new class implements MigrationInterface
{
    /**
     * Run the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function up(SchemaBuilder $schema): void
    {
        $schema->table('{{ table }}', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migration
     *
     * @param SchemaBuilder $schema
     * @return void
     */
    public function down(SchemaBuilder $schema): void
    {
        $schema->table('{{ table }}', function (Blueprint $table) {
            //
        });
    }
};
STUB;
    }

    /**
     * Populate the stub with the given values
     *
     * @param string $stub
     * @param string $name
     * @param string|null $table
     * @return string
     */
    protected function populateStub(string $stub, string $name, ?string $table): string
    {
        $className = $this->getClassName($name);

        $stub = str_replace('{{ class }}', $className, $stub);
        $stub = str_replace('{{class}}', $className, $stub);

        if ($table !== null) {
            $stub = str_replace('{{ table }}', $table, $stub);
            $stub = str_replace('{{table}}', $table, $stub);
        }

        return $stub;
    }

    /**
     * Get the class name from the migration name
     *
     * @param string $name
     * @return string
     */
    protected function getClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * Get the date prefix for the migration
     *
     * @return string
     */
    protected function getDatePrefix(): string
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs
     *
     * @return string
     */
    public function getStubPath(): string
    {
        return $this->stubPath;
    }

    /**
     * Set the path to the stubs
     *
     * @param string $path
     * @return void
     */
    public function setStubPath(string $path): void
    {
        $this->stubPath = $path;
    }
}
