<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Manager;

use Windwalker\Database\Schema\DataType;
use Windwalker\Database\Schema\Meta\Column;
use Windwalker\Database\Schema\Meta\Key;
use Windwalker\Database\Schema\Schema;
use Windwalker\Database\Schema\SchemaManager;

/**
 * The TableManager class.
 */
class TableManager extends AbstractMetaManager
{
    /**
     * @var Column[]
     */
    protected array $columns = [];

    /**
     * @var string|null
     */
    protected string $schema;

    /**
     * create
     *
     * @param  callable|Schema  $callback
     * @param  bool             $ifNotExists
     * @param  array            $options
     *
     * @return  static
     */
    public function create($callback, bool $ifNotExists = true, array $options = []): static
    {
        $this->getPlatform()->createTable(
            $this->callSchema($callback),
            $ifNotExists,
            $options
        );

        $this->getSchema()->reset();

        return $this;
    }

    /**
     * update
     *
     * @param  callable|Schema  $schema
     *
     * @return  static
     */
    public function update($schema): static
    {
        $schema = $this->callSchema($schema);

        foreach ($schema->getColumns() as $column) {
            if ($this->hasColumn($column->getName())) {
                $this->modifyColumn($column);
            } else {
                $this->addColumn($column);
            }
        }

        foreach ($schema->getIndexes() as $index) {
            $this->addIndex($index);
        }

        return $this->reset();
    }

    /**
     * save
     *
     * @param  callable|Schema  $schema
     * @param  bool             $ifNotExists
     * @param  array            $options
     *
     * @return  static
     */
    public function save($schema, bool $ifNotExists = true, array $options = [])
    {
        $schema = $this->callSchema($schema);

        if ($this->exists()) {
            $this->update($schema);
        } else {
            $this->create($schema, $ifNotExists, $options);
        }

        return $this->reset();
    }

    /**
     * drop
     *
     * @param  bool  $ifExists
     *
     * @return  static
     */
    public function drop(bool $ifExists = true)
    {
        $this->getPlatform()->dropTable(
            $this->getName(),
            $ifExists
        );

        return $this->reset();
    }

    /**
     * exists
     *
     * @return  bool
     */
    public function exists(): bool
    {
        return isset($this->getPlatform()->listTables()[$this->getName()]);
    }

    /**
     * rename
     *
     * @param  string   $newName
     * @param  boolean  $returnNew
     *
     * @return  static
     */
    public function rename($newName, $returnNew = true)
    {
        $this->getPlatform()->renameTable($this->getName(), $newName);

        if ($returnNew) {
            return $this->db->getTable($newName, true);
        }

        $this->name = $newName;

        return $this;
    }

    /**
     * Method to truncate a table.
     *
     * @return  static
     *
     * @throws  \RuntimeException
     * @since   2.0
     */
    public function truncate()
    {
        $this->db->execute('TRUNCATE TABLE ' . $this->db->quoteName($this->getName()));

        return $this;
    }

    /**
     * getDetail
     *
     * @return  array
     */
    public function getDetail(): array
    {
        return $this->getPlatform()->getTableDetail($this->getName());
    }

    /**
     * Get table columns.
     *
     * @param  bool  $refresh
     *
     * @return array Table columns with type.
     */
    public function getColumnNames(bool $refresh = false): array
    {
        return array_keys($this->getColumns($refresh));
    }

    /**
     * getColumnDetails
     *
     * @param  bool  $refresh
     *
     * @return Column[]
     */
    public function getColumns(bool $refresh = false): array
    {
        if ($this->columns === null || $refresh) {
            $this->columns = Column::wrapList(
                $this->getPlatform()
                    ->listColumns(
                        $this->getName(),
                        $this->getPlatform()::getDefaultSchema()
                    )
            );
        }

        return $this->columns;
    }

    /**
     * getColumn
     *
     * @param  string  $name
     *
     * @return Column|null
     */
    public function getColumn(string $name): ?Column
    {
        return $this->getColumns()[$name] ?? null;
    }

    /**
     * hasColumn
     *
     * @param  string  $name
     *
     * @return  bool
     */
    public function hasColumn(string $name): bool
    {
        return $this->getColumn($name) !== null;
    }

    /**
     * addColumn
     *
     * @param  string|Column  $column
     * @param  string         $dataType
     * @param  bool           $isNullable
     * @param  null           $columnDefault
     * @param  array          $options
     *
     * @return static
     */
    public function addColumn(
        $column = '',
        string $dataType = 'char',
        bool $isNullable = false,
        $columnDefault = null,
        array $options = []
    ) {
        if (!$column instanceof Column) {
            $column = new Column($column, $dataType, $isNullable, $columnDefault, $options);
        }

        $this->getPlatform()->addColumn($column);

        return $this;
    }

    /**
     * dropColumn
     *
     * @param  string  $name
     *
     * @return  static
     */
    public function dropColumn(string $name)
    {
        if (!$this->hasColumn($name)) {
            return $this;
        }

        $this->getPlatform()->dropColumn($name);

        return $this->reset();
    }

    /**
     * modifyColumn
     *
     * @param  string|Column  $column
     * @param  string         $dataType
     * @param  bool           $isNullable
     * @param  null           $columnDefault
     * @param  array          $options
     *
     * @return void
     */
    public function modifyColumn(
        $column = '',
        string $dataType = 'char',
        bool $isNullable = false,
        $columnDefault = null,
        array $options = []
    ) {
        if (!$column instanceof Column) {
            $column = new Column($column, $dataType, $isNullable, $columnDefault, $options);
        }

        $this->getPlatform()->modifyColumn($column);
    }

    /**
     * addIndex
     *
     * @param  array|Key  $columns
     * @param  string     $name
     * @param  string     $type
     * @param  array      $options
     *
     * @return void
     */
    public function addIndex($columns = [], ?string $name = null, string $type = Key::TYPE_INDEX, array $options = [])
    {
        $columns = (array) $columns;



        if (!$columns instanceof Key) {
            $column = new Key($type, $columns, $name, $options);
        }


    }

    /**
     * dropIndex
     *
     * @param  string  $name
     *
     * @return  static
     */
    public function dropIndex($name)
    {
    }

    /**
     * getIndexes
     *
     * @return  array
     */
    public function getIndexes()
    {
    }

    /**
     * hasIndex
     *
     * @param  string  $name
     *
     * @return  boolean
     */
    public function hasIndex($name)
    {
        $indexes = $this->getIndexes();

        foreach ($indexes as $index) {
            if ($index->Key_name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method to set property table
     *
     * @param  string  $name
     *
     * @return  static  Return self to support chaining.
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getSchema
     *
     * @param  bool  $new
     *
     * @return  SchemaManager
     */
    public function getSchema(bool $new = false): SchemaManager
    {
        return $this->db->getSchema($this->schema, $new);
    }

    /**
     * getSchema
     *
     * @return  Schema
     */
    public function getSchemaObject()
    {
        return new Schema($this);
    }

    /**
     * callSchema
     *
     * @param  callable|Schema  $callback
     *
     * @return  Schema
     */
    protected function callSchema($callback)
    {
        if (is_callable($callback)) {
            $callback($schema = $this->getSchemaObject());
        } else {
            $schema = $callback;
        }

        if (!$schema instanceof Schema) {
            throw new \InvalidArgumentException('Argument 1 should be Schema object.');
        }

        return $schema;
    }

    /**
     * Method to get property Database
     *
     * @return  AbstractDatabase
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Method to set property database
     *
     * @param  AbstractDatabase|string  $database
     *
     * @return  static  Return self to support chaining.
     */
    public function setDatabase($database)
    {
        if (is_string($database)) {
            $database = $this->db->getDatabase($database);
        }

        $this->database = $database;

        return $this;
    }

    /**
     * getTypeMapper
     *
     * @return  DataType
     */
    public function getDataType()
    {
        $driver = ucfirst($this->db->getName());

        $class = sprintf('Windwalker\Database\Driver\%s\%sType', $driver, $driver);

        return new $class();
    }

    /**
     * prepareColumn
     *
     * @param  Column  $column
     *
     * @return  Column
     */
    protected function prepareColumn(Column $column)
    {
        $typeMapper = $this->getDataType();

        $type   = $typeMapper::getType($column->getType());
        $length = $column->getLength() ?: $typeMapper::getLength($type);

        $length = $length ? '(' . $length . ')' : null;

        $column->type($type);

        // Prepare default value
        $this->prepareDefaultValue($column);

        return $column->length($length);
    }

    /**
     * prepareDefaultValue
     *
     * @param  Column  $column
     *
     * @return  Column
     */
    protected function prepareDefaultValue(Column $column)
    {
        $typeMapper = $this->getDataType();

        $default = $column->getDefault();

        if (!$column->getNullable() && $default === null && !$column->isPrimary()) {
            $default = $typeMapper::getDefaultValue($column->getType());

            $column->defaultValue($default);
        }

        return $column;
    }

    /**
     * reset
     *
     * @return  static
     */
    public function reset()
    {
        $this->columnCache = [];
        $this->indexCache  = [];
        $this->database    = null;

        return $this;
    }
}
