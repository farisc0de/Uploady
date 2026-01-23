<?php

namespace Farisc0de\PhpMigration\Schema;

/**
 * Class Blueprint
 * 
 * Provides a fluent interface for defining database table schemas
 */
class Blueprint
{
    /**
     * The table name
     *
     * @var string
     */
    protected string $table;

    /**
     * The columns that should be added to the table
     *
     * @var array<ColumnDefinition>
     */
    protected array $columns = [];

    /**
     * The commands to be run for the table
     *
     * @var array
     */
    protected array $commands = [];

    /**
     * The storage engine for the table (MySQL)
     *
     * @var string|null
     */
    public ?string $engine = null;

    /**
     * The default character set for the table
     *
     * @var string|null
     */
    public ?string $charset = null;

    /**
     * The collation for the table
     *
     * @var string|null
     */
    public ?string $collation = null;

    /**
     * Whether the table is temporary
     *
     * @var bool
     */
    public bool $temporary = false;

    /**
     * The table comment
     *
     * @var string|null
     */
    public ?string $comment = null;

    /**
     * Create a new blueprint instance
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the columns
     *
     * @return array<ColumnDefinition>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the commands
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Add a command to the blueprint
     *
     * @param string $name
     * @param array $parameters
     * @return array
     */
    protected function addCommand(string $name, array $parameters = []): array
    {
        $command = array_merge(['name' => $name], $parameters);
        $this->commands[] = $command;
        return $command;
    }

    /**
     * Add a new column to the blueprint
     *
     * @param string $type
     * @param string $name
     * @param array $parameters
     * @return ColumnDefinition
     */
    protected function addColumn(string $type, string $name, array $parameters = []): ColumnDefinition
    {
        $column = new ColumnDefinition(
            array_merge(compact('type', 'name'), $parameters)
        );

        $this->columns[] = $column;

        return $column;
    }

    // ==================== ID & Primary Key Methods ====================

    /**
     * Create an auto-incrementing big integer (8-byte) primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function id(string $column = 'id'): ColumnDefinition
    {
        return $this->bigIncrements($column);
    }

    /**
     * Create an auto-incrementing integer primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function increments(string $column): ColumnDefinition
    {
        return $this->unsignedInteger($column)->autoIncrement()->primary();
    }

    /**
     * Create an auto-incrementing tiny integer primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function tinyIncrements(string $column): ColumnDefinition
    {
        return $this->unsignedTinyInteger($column)->autoIncrement()->primary();
    }

    /**
     * Create an auto-incrementing small integer primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function smallIncrements(string $column): ColumnDefinition
    {
        return $this->unsignedSmallInteger($column)->autoIncrement()->primary();
    }

    /**
     * Create an auto-incrementing medium integer primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function mediumIncrements(string $column): ColumnDefinition
    {
        return $this->unsignedMediumInteger($column)->autoIncrement()->primary();
    }

    /**
     * Create an auto-incrementing big integer primary key
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function bigIncrements(string $column): ColumnDefinition
    {
        return $this->unsignedBigInteger($column)->autoIncrement()->primary();
    }

    // ==================== Integer Types ====================

    /**
     * Create a new integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function integer(string $column): ColumnDefinition
    {
        return $this->addColumn('integer', $column);
    }

    /**
     * Create a new tiny integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function tinyInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('tinyInteger', $column);
    }

    /**
     * Create a new small integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function smallInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('smallInteger', $column);
    }

    /**
     * Create a new medium integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function mediumInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('mediumInteger', $column);
    }

    /**
     * Create a new big integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function bigInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('bigInteger', $column);
    }

    /**
     * Create a new unsigned integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function unsignedInteger(string $column): ColumnDefinition
    {
        return $this->integer($column)->unsigned();
    }

    /**
     * Create a new unsigned tiny integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function unsignedTinyInteger(string $column): ColumnDefinition
    {
        return $this->tinyInteger($column)->unsigned();
    }

    /**
     * Create a new unsigned small integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function unsignedSmallInteger(string $column): ColumnDefinition
    {
        return $this->smallInteger($column)->unsigned();
    }

    /**
     * Create a new unsigned medium integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function unsignedMediumInteger(string $column): ColumnDefinition
    {
        return $this->mediumInteger($column)->unsigned();
    }

    /**
     * Create a new unsigned big integer column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function unsignedBigInteger(string $column): ColumnDefinition
    {
        return $this->bigInteger($column)->unsigned();
    }

    // ==================== String Types ====================

    /**
     * Create a new char column
     *
     * @param string $column
     * @param int $length
     * @return ColumnDefinition
     */
    public function char(string $column, int $length = 255): ColumnDefinition
    {
        return $this->addColumn('char', $column, compact('length'));
    }

    /**
     * Create a new varchar column
     *
     * @param string $column
     * @param int $length
     * @return ColumnDefinition
     */
    public function string(string $column, int $length = 255): ColumnDefinition
    {
        return $this->addColumn('string', $column, compact('length'));
    }

    /**
     * Create a new text column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function text(string $column): ColumnDefinition
    {
        return $this->addColumn('text', $column);
    }

    /**
     * Create a new tiny text column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function tinyText(string $column): ColumnDefinition
    {
        return $this->addColumn('tinyText', $column);
    }

    /**
     * Create a new medium text column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function mediumText(string $column): ColumnDefinition
    {
        return $this->addColumn('mediumText', $column);
    }

    /**
     * Create a new long text column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function longText(string $column): ColumnDefinition
    {
        return $this->addColumn('longText', $column);
    }

    // ==================== Numeric Types ====================

    /**
     * Create a new float column
     *
     * @param string $column
     * @param int $precision
     * @param int $scale
     * @return ColumnDefinition
     */
    public function float(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn('float', $column, compact('precision', 'scale'));
    }

    /**
     * Create a new double column
     *
     * @param string $column
     * @param int|null $precision
     * @param int|null $scale
     * @return ColumnDefinition
     */
    public function double(string $column, ?int $precision = null, ?int $scale = null): ColumnDefinition
    {
        return $this->addColumn('double', $column, compact('precision', 'scale'));
    }

    /**
     * Create a new decimal column
     *
     * @param string $column
     * @param int $precision
     * @param int $scale
     * @return ColumnDefinition
     */
    public function decimal(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn('decimal', $column, compact('precision', 'scale'));
    }

    /**
     * Create a new unsigned decimal column
     *
     * @param string $column
     * @param int $precision
     * @param int $scale
     * @return ColumnDefinition
     */
    public function unsignedDecimal(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->decimal($column, $precision, $scale)->unsigned();
    }

    // ==================== Boolean ====================

    /**
     * Create a new boolean column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function boolean(string $column): ColumnDefinition
    {
        return $this->addColumn('boolean', $column);
    }

    // ==================== Date/Time Types ====================

    /**
     * Create a new date column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function date(string $column): ColumnDefinition
    {
        return $this->addColumn('date', $column);
    }

    /**
     * Create a new datetime column
     *
     * @param string $column
     * @param int $precision
     * @return ColumnDefinition
     */
    public function dateTime(string $column, int $precision = 0): ColumnDefinition
    {
        return $this->addColumn('dateTime', $column, compact('precision'));
    }

    /**
     * Create a new time column
     *
     * @param string $column
     * @param int $precision
     * @return ColumnDefinition
     */
    public function time(string $column, int $precision = 0): ColumnDefinition
    {
        return $this->addColumn('time', $column, compact('precision'));
    }

    /**
     * Create a new timestamp column
     *
     * @param string $column
     * @param int $precision
     * @return ColumnDefinition
     */
    public function timestamp(string $column, int $precision = 0): ColumnDefinition
    {
        return $this->addColumn('timestamp', $column, compact('precision'));
    }

    /**
     * Create a new year column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function year(string $column): ColumnDefinition
    {
        return $this->addColumn('year', $column);
    }

    /**
     * Add created_at and updated_at timestamps
     *
     * @param int $precision
     * @return void
     */
    public function timestamps(int $precision = 0): void
    {
        $this->timestamp('created_at', $precision)->nullable()->useCurrent();
        $this->timestamp('updated_at', $precision)->nullable()->useCurrentOnUpdate();
    }

    /**
     * Add nullable created_at and updated_at timestamps
     *
     * @param int $precision
     * @return void
     */
    public function nullableTimestamps(int $precision = 0): void
    {
        $this->timestamps($precision);
    }

    /**
     * Add a deleted_at timestamp for soft deletes
     *
     * @param string $column
     * @param int $precision
     * @return ColumnDefinition
     */
    public function softDeletes(string $column = 'deleted_at', int $precision = 0): ColumnDefinition
    {
        return $this->timestamp($column, $precision)->nullable();
    }

    // ==================== Binary Types ====================

    /**
     * Create a new binary column
     *
     * @param string $column
     * @param int|null $length
     * @return ColumnDefinition
     */
    public function binary(string $column, ?int $length = null): ColumnDefinition
    {
        return $this->addColumn('binary', $column, compact('length'));
    }

    /**
     * Create a new blob column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function blob(string $column): ColumnDefinition
    {
        return $this->addColumn('blob', $column);
    }

    /**
     * Create a new tiny blob column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function tinyBlob(string $column): ColumnDefinition
    {
        return $this->addColumn('tinyBlob', $column);
    }

    /**
     * Create a new medium blob column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function mediumBlob(string $column): ColumnDefinition
    {
        return $this->addColumn('mediumBlob', $column);
    }

    /**
     * Create a new long blob column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function longBlob(string $column): ColumnDefinition
    {
        return $this->addColumn('longBlob', $column);
    }

    // ==================== Special Types ====================

    /**
     * Create a new enum column
     *
     * @param string $column
     * @param array $allowed
     * @return ColumnDefinition
     */
    public function enum(string $column, array $allowed): ColumnDefinition
    {
        return $this->addColumn('enum', $column, compact('allowed'));
    }

    /**
     * Create a new set column
     *
     * @param string $column
     * @param array $allowed
     * @return ColumnDefinition
     */
    public function set(string $column, array $allowed): ColumnDefinition
    {
        return $this->addColumn('set', $column, compact('allowed'));
    }

    /**
     * Create a new JSON column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function json(string $column): ColumnDefinition
    {
        return $this->addColumn('json', $column);
    }

    /**
     * Create a new JSONB column (PostgreSQL)
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function jsonb(string $column): ColumnDefinition
    {
        return $this->addColumn('jsonb', $column);
    }

    /**
     * Create a new UUID column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function uuid(string $column = 'uuid'): ColumnDefinition
    {
        return $this->addColumn('uuid', $column);
    }

    /**
     * Create a new IP address column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function ipAddress(string $column = 'ip_address'): ColumnDefinition
    {
        return $this->string($column, 45);
    }

    /**
     * Create a new MAC address column
     *
     * @param string $column
     * @return ColumnDefinition
     */
    public function macAddress(string $column = 'mac_address'): ColumnDefinition
    {
        return $this->string($column, 17);
    }

    // ==================== Foreign Key Helpers ====================

    /**
     * Create a foreign ID column
     *
     * @param string $column
     * @return ForeignIdColumnDefinition
     */
    public function foreignId(string $column): ForeignIdColumnDefinition
    {
        return new ForeignIdColumnDefinition(
            $this,
            $this->unsignedBigInteger($column)
        );
    }

    /**
     * Create a foreign UUID column
     *
     * @param string $column
     * @return ForeignIdColumnDefinition
     */
    public function foreignUuid(string $column): ForeignIdColumnDefinition
    {
        return new ForeignIdColumnDefinition(
            $this,
            $this->uuid($column)
        );
    }

    // ==================== Index Methods ====================

    /**
     * Specify a primary key
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function primary(string|array $columns, ?string $name = null): self
    {
        $this->addCommand('primary', compact('columns', 'name'));
        return $this;
    }

    /**
     * Specify a unique index
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function unique(string|array $columns, ?string $name = null): self
    {
        $this->addCommand('unique', compact('columns', 'name'));
        return $this;
    }

    /**
     * Specify an index
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function index(string|array $columns, ?string $name = null): self
    {
        $this->addCommand('index', compact('columns', 'name'));
        return $this;
    }

    /**
     * Specify a fulltext index
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function fullText(string|array $columns, ?string $name = null): self
    {
        $this->addCommand('fullText', compact('columns', 'name'));
        return $this;
    }

    /**
     * Specify a spatial index
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function spatialIndex(string|array $columns, ?string $name = null): self
    {
        $this->addCommand('spatialIndex', compact('columns', 'name'));
        return $this;
    }

    /**
     * Specify a foreign key
     *
     * @param string|array $columns
     * @param string|null $name
     * @return ForeignKeyDefinition
     */
    public function foreign(string|array $columns, ?string $name = null): ForeignKeyDefinition
    {
        $command = $this->addCommand('foreign', compact('columns', 'name'));
        return new ForeignKeyDefinition($this, $command);
    }

    // ==================== Drop Index Methods ====================

    /**
     * Drop a primary key
     *
     * @param string|null $name
     * @return $this
     */
    public function dropPrimary(?string $name = null): self
    {
        $this->addCommand('dropPrimary', compact('name'));
        return $this;
    }

    /**
     * Drop a unique index
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropUnique(string|array $columns): self
    {
        $this->addCommand('dropUnique', ['columns' => $columns]);
        return $this;
    }

    /**
     * Drop an index
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropIndex(string|array $columns): self
    {
        $this->addCommand('dropIndex', ['columns' => $columns]);
        return $this;
    }

    /**
     * Drop a fulltext index
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropFullText(string|array $columns): self
    {
        $this->addCommand('dropFullText', ['columns' => $columns]);
        return $this;
    }

    /**
     * Drop a spatial index
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropSpatialIndex(string|array $columns): self
    {
        $this->addCommand('dropSpatialIndex', ['columns' => $columns]);
        return $this;
    }

    /**
     * Drop a foreign key
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropForeign(string|array $columns): self
    {
        $this->addCommand('dropForeign', ['columns' => $columns]);
        return $this;
    }

    // ==================== Column Modification ====================

    /**
     * Drop columns from the table
     *
     * @param string|array $columns
     * @return $this
     */
    public function dropColumn(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->addCommand('dropColumn', compact('columns'));
        return $this;
    }

    /**
     * Rename a column
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function renameColumn(string $from, string $to): self
    {
        $this->addCommand('renameColumn', compact('from', 'to'));
        return $this;
    }

    // ==================== Table Options ====================

    /**
     * Set the storage engine for the table
     *
     * @param string $engine
     * @return $this
     */
    public function engine(string $engine): self
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * Set the character set for the table
     *
     * @param string $charset
     * @return $this
     */
    public function charset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set the collation for the table
     *
     * @param string $collation
     * @return $this
     */
    public function collation(string $collation): self
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * Set the table as temporary
     *
     * @return $this
     */
    public function temporary(): self
    {
        $this->temporary = true;
        return $this;
    }

    /**
     * Add a comment to the table
     *
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    // ==================== Morphs ====================

    /**
     * Add the proper columns for a polymorphic relationship
     *
     * @param string $name
     * @param string|null $indexName
     * @return void
     */
    public function morphs(string $name, ?string $indexName = null): void
    {
        $this->unsignedBigInteger("{$name}_id");
        $this->string("{$name}_type");
        $this->index(["{$name}_id", "{$name}_type"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic relationship
     *
     * @param string $name
     * @param string|null $indexName
     * @return void
     */
    public function nullableMorphs(string $name, ?string $indexName = null): void
    {
        $this->unsignedBigInteger("{$name}_id")->nullable();
        $this->string("{$name}_type")->nullable();
        $this->index(["{$name}_id", "{$name}_type"], $indexName);
    }

    /**
     * Add UUID columns for a polymorphic relationship
     *
     * @param string $name
     * @param string|null $indexName
     * @return void
     */
    public function uuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->uuid("{$name}_id");
        $this->string("{$name}_type");
        $this->index(["{$name}_id", "{$name}_type"], $indexName);
    }

    /**
     * Add nullable UUID columns for a polymorphic relationship
     *
     * @param string $name
     * @param string|null $indexName
     * @return void
     */
    public function nullableUuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->uuid("{$name}_id")->nullable();
        $this->string("{$name}_type")->nullable();
        $this->index(["{$name}_id", "{$name}_type"], $indexName);
    }

    // ==================== Remember Token ====================

    /**
     * Add a remember token column
     *
     * @return ColumnDefinition
     */
    public function rememberToken(): ColumnDefinition
    {
        return $this->string('remember_token', 100)->nullable();
    }
}
