<?php

namespace Farisc0de\PhpMigration\Seeders;

use InvalidArgumentException;

/**
 * Class SeederCreator
 * 
 * Creates new seeder files from stubs
 */
class SeederCreator
{
    /**
     * The path to the stubs directory
     *
     * @var string
     */
    protected string $stubPath;

    /**
     * Create a new seeder creator instance
     *
     * @param string|null $stubPath
     */
    public function __construct(?string $stubPath = null)
    {
        $this->stubPath = $stubPath ?? dirname(__DIR__, 2) . '/stubs';
    }

    /**
     * Create a new seeder file
     *
     * @param string $name The name of the seeder
     * @param string $path The path where the seeder should be created
     * @return string The path to the created seeder file
     */
    public function create(string $name, string $path): string
    {
        $this->ensureSeederDoesntAlreadyExist($name, $path);

        $stub = $this->getStub();

        $filename = $name . '.php';
        $filepath = $path . '/' . $filename;

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $content = $this->populateStub($stub, $name);

        file_put_contents($filepath, $content);

        return $filepath;
    }

    /**
     * Ensure that a seeder with the given name doesn't already exist
     *
     * @param string $name
     * @param string $path
     * @return void
     * @throws InvalidArgumentException
     */
    protected function ensureSeederDoesntAlreadyExist(string $name, string $path): void
    {
        $filepath = $path . '/' . $name . '.php';

        if (file_exists($filepath)) {
            throw new InvalidArgumentException("A seeder with the name '{$name}' already exists.");
        }
    }

    /**
     * Get the seeder stub file
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stubFile = $this->stubPath . '/seeder.stub';

        if (!file_exists($stubFile)) {
            return $this->getDefaultStub();
        }

        return file_get_contents($stubFile);
    }

    /**
     * Get the default stub content
     *
     * @return string
     */
    protected function getDefaultStub(): string
    {
        return <<<'STUB'
<?php

namespace Database\Seeders;

use Farisc0de\PhpMigration\Seeders\Seeder;

class {{ class }} extends Seeder
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run(): void
    {
        // Example: Insert data
        // $this->insert('users', [
        //     ['name' => 'Admin', 'email' => 'admin@example.com'],
        //     ['name' => 'User', 'email' => 'user@example.com'],
        // ]);

        // Example: Call other seeders
        // $this->call(AnotherSeeder::class);
    }
}
STUB;
    }

    /**
     * Populate the stub with the given values
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function populateStub(string $stub, string $name): string
    {
        $stub = str_replace('{{ class }}', $name, $stub);
        $stub = str_replace('{{class}}', $name, $stub);

        return $stub;
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
