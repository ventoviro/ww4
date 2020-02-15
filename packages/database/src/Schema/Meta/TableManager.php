<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

use Windwalker\Database\Schema\Schema;

/**
 * The TableManager class.
 */
class TableManager extends AbstractMetaManager
{
    /**
     * @var Column[]
     */
    protected $columns = null;

    /**
     * create
     *
     * @param   callable|Schema $callback
     * @param   bool            $ifNotExists
     * @param   array           $options
     *
     * @return  static
     */
    public function create($callback, $ifNotExists = true, $options = [])
    {

    }

    /**
     * update
     *
     * @param   callable|Schema $schema
     *
     * @return  static
     */
    public function update($schema)
    {

    }

    /**
     * save
     *
     * @param   callable|Schema $schema
     * @param   bool            $ifNotExists
     * @param   array           $options
     *
     * @return  static
     */
    public function save($schema, $ifNotExists = true, $options = [])
    {
        $schema = $this->callSchema($schema);

        if ($this->exists()) {
            $this->update($schema);
        } else {
            $this->create($schema, $ifNotExists, $options);
        }

        $database = $this->db->getDatabase();
        $database->reset();

        return $this->reset();
    }

    /**
     * drop
     *
     * @param bool   $ifExists
     * @param string $option
     *
     * @return  static
     */
    public function drop($ifExists = true, $option = '')
    {


        return $this->reset();
    }

    /**
     * exists
     *
     * @return  boolean
     */
    public function exists()
    {
        return $database->tableExists($this->getName());
    }

    /**
     * rename
     *
     * @param string  $newName
     * @param boolean $returnNew
     *
     * @return  static
     */
    public function rename($newName, $returnNew = true)
    {

    }

    /**
     * Method to truncate a table.
     *
     * @return  static
     *
     * @since   2.0
     * @throws  \RuntimeException
     */
    public function truncate()
    {
        $this->db->setQuery('TRUNCATE TABLE ' . $this->db->quoteName($this->getName()))->execute();

        return $this;
    }

    /**
     * getDetail
     *
     * @return  array|boolean
     */
    public function getDetail()
    {
        return $this->db->getDatabase()->getTableDetail($this->getName());
    }

    /**
     * Get table columns.
     *
     * @param bool $refresh
     *
     * @return array Table columns with type.
     */
    public function getColumnNames($refresh = false)
    {
        return array_keys($this->getColumns($refresh));
    }

    /**
     * getColumnDetails
     *
     * @param bool $refresh
     *
     * @return Column[]
     */
    public function getColumns($refresh = false)
    {
        if ($this->columns === null || $refresh) {
            $this->columns = Column::wrapList(
                $this->db->getSchemaManager()
                    ->listColumns(
                        $this->getName(),
                        $this->db->getPlatform()::getDefaultSchema()
                    )
            );
        }

        return $this->columns;
    }

    /**
     * getColumn
     *
     * @param   string $name
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
     * @param   string  $name
     *
     * @return  bool
     */
    public function hasColumn(string $name)
    {
        return $this->getColumn($name) !== null;
    }

    /**
     * addColumn
     *
     * @param  string|Column  $column
     * @param  string  $dataType
     * @param  bool    $isNullable
     * @param  null    $columnDefault
     * @param  array   $options
     *
     * @return void
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

        $this->db->getSchemaManager()->addColumn($column);
    }

    /**
     * dropColumn
     *
     * @param string $name
     *
     * @return  static
     */
    public function dropColumn($name)
    {
        if (!$this->hasColumn($name)) {
            return $this;
        }

        $builder = $this->db->getQuery(true)->getGrammar();

        $query = $builder::dropColumn($this->getName(), $name);

        $this->db->setQuery($query)->execute();

        return $this->reset();
    }

    /**
     * modifyColumn
     *
     * @param string|Column $name
     * @param string        $type
     * @param bool          $signed
     * @param bool          $allowNull
     * @param string        $default
     * @param string        $comment
     * @param array         $options
     *
     * @return  static
     */
    abstract public function modifyColumn(
        $name,
        $type = 'text',
        $signed = true,
        $allowNull = true,
        $default = '',
        $comment = '',
        $options = []
    );

    /**
     * changeColumn
     *
     * @param string        $oldName
     * @param string|Column $newName
     * @param string        $type
     * @param bool          $signed
     * @param bool          $allowNull
     * @param string        $default
     * @param string        $comment
     * @param array         $options
     *
     * @return  static
     */
    abstract public function changeColumn(
        $oldName,
        $newName,
        $type = 'text',
        $signed = true,
        $allowNull = true,
        $default = '',
        $comment = '',
        $options = []
    );

    /**
     * addIndex
     *
     * @param string $type
     * @param array  $columns
     * @param string $name
     * @param string $comment
     * @param array  $options
     *
     * @return static
     */
    abstract public function addIndex($type, $columns = [], $name = null, $comment = null, $options = []);

    /**
     * dropIndex
     *
     * @param string $name
     *
     * @return  static
     */
    abstract public function dropIndex($name);

    /**
     * getIndexes
     *
     * @return  array
     */
    abstract public function getIndexes();

    /**
     * hasIndex
     *
     * @param   string $name
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
     * Method to get property Table
     *
     * @return  string
     */
    public function getName()
    {
        if ($this->database instanceof AbstractDatabase
            && $this->database->getName() !== $this->db->getCurrentDatabase()) {
            return $this->database->getName() . '.' . $this->name;
        }

        return $this->name;
    }

    /**
     * Method to set property table
     *
     * @param   string $name
     *
     * @return  static  Return self to support chaining.
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to get property Db
     *
     * @return  \Windwalker\Database\Driver\AbstractDatabaseDriver
     */
    public function getDriver()
    {
        return $this->db;
    }

    /**
     * Method to set property db
     *
     * @param   \Windwalker\Database\Driver\AbstractDatabaseDriver $db
     *
     * @return  static  Return self to support chaining.
     */
    public function setDriver($db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * getSchema
     *
     * @return  Schema
     */
    public function getSchema()
    {
        return '';
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
     * @param   callable|Schema $schema
     *
     * @return  Schema
     */
    protected function callSchema($schema)
    {
        if (!$schema instanceof Schema && is_callable($schema)) {
            $s = $this->getSchema();

            $schema($s);

            $schema = $s;
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
     * @param   AbstractDatabase|string $database
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
     * @param Column $column
     *
     * @return  Column
     */
    protected function prepareColumn(Column $column)
    {
        $typeMapper = $this->getDataType();

        $type = $typeMapper::getType($column->getType());
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
     * @param Column $column
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
        $this->indexCache = [];
        $this->database = null;

        return $this;
    }
}
