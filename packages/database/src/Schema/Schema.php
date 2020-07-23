<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Database\Manager\TableManager;
use Windwalker\Database\Schema\Concern\DataTypeTrait;
use Windwalker\Database\Schema\Meta\Column;
use Windwalker\Database\Schema\Meta\Constraint;
use Windwalker\Database\Schema\Meta\Key;

/**
 * The Schema class.
 *
 * @method  Column  bigint(string $name)
 * @method  Column  bit(string $name)
 * @method  Column  char(string $name)
 * @method  Column  datetime(string $name)
 * @method  Column  date(string $name)
 * @method  Column  decimal(string $name)
 * @method  Column  double(string $name)
 * @method  Column  float(string $name)
 * @method  Column  integer(string $name)
 * @method  Column  longtext(string $name)
 * @method  Column  primary(string $name)
 * @method  Column  text(string $name)
 * @method  Column  timestamp(string $name)
 * @method  Column  tinyint(string $name)
 * @method  Column  varchar(string $name)
 *
 * @since  2.1.8
 */
class Schema
{
    /**
     * Property columns.
     *
     * @var  Column[]
     */
    protected array $columns = [];

    /**
     * Property indexes.
     *
     * @var  Key[]
     */
    protected array $indexes = [];

    /**
     * Property table.
     *
     * @var  TableManager
     */
    protected TableManager $table;

    /**
     * Schema constructor.
     *
     * @param  TableManager  $table
     */
    public function __construct(TableManager $table)
    {
        $this->table = $table;
    }

    public function add(string $name, Column|string $column): Column
    {
        $column->name($name);

        return $this->addColumn($column);
    }

    public function addColumn(Column|string $column): Column
    {
        if (is_string($column) && class_exists($column)) {
            $column = new $column();
        }

        if (!$column instanceof Column) {
            throw new \InvalidArgumentException(__METHOD__ . ' argument 1 need Column instance.');
        }

        $this->columns[$column->getName()] = $column;

        return $column;
    }

    /**
     * addKey
     *
     * @param  Key  $key
     *
     * @return  Key
     */
    public function addKey(Key $key): Key
    {
        $name = $key->getName();

        if (!$name) {
            $columns = $key->getColumns();

            $columns = array_map(
                static fn($col) => explode('(', $col)[0],
                $columns
            );

            $name = 'idx_' . trim($this->table->getName(), '#_') . '_' . implode('_', $columns);

            $key->name($name);
        }

        $this->indexes[$key->getName()] = $key;

        return $key;
    }

    public function addIndex(array|string $columns, ?string $name = null): Key
    {
        return $this->addKey(new Key(Key::TYPE_INDEX, (array) $columns, $name));
    }

    public function addUniqueKey(array|string $columns, ?string $name = null): Constraint
    {
        // return $this->addKey(new Key(Key::TYPE_UNIQUE, (array) $columns, $name));
    }

    /**
     * addPrimaryKey
     *
     * @param  array|string  $columns
     *
     * @return Key
     */
    public function addPrimaryKey(array|string $columns)
    {
        return $this->addKey(new Key(Key::TYPE_PRIMARY, (array) $columns, null));
    }

    public function getTable(): TableManager
    {
        return $this->table;
    }

    public function setTable(TableManager $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return  Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Method to set property columns
     *
     * @param  Column[]  $columns
     *
     * @return  static  Return self to support chaining.
     */
    public function setColumns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Method to get property Indexes
     *
     * @return  Key[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Method to set property indexes
     *
     * @param  Key[]  $indexes
     *
     * @return  static  Return self to support chaining.
     */
    public function setIndexes(array $indexes): static
    {
        $this->indexes = $indexes;

        return $this;
    }

    /**
     * getDateFormat
     *
     * @return  string
     *
     * @since   3.0
     */
    public function getDateFormat(): string
    {
        // return $this->getTable()->getDriver()->getQuery(true)->getDateFormat();
    }

    /**
     * getNullDate
     *
     * @return  string
     *
     * @since   3.0
     */
    public function getNullDate()
    {
        // return $this->getTable()->getDriver()->getQuery(true)->getNullDate();
    }

    public function __call(string $name, array $args)
    {
        $column = array_shift($args);

        return $this->addColumn(new Column($column));
    }
}
