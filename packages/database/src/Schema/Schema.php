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
use Windwalker\Database\Schema\Ddl\Column;
use Windwalker\Database\Schema\Ddl\Constraint;
use Windwalker\Database\Schema\Ddl\Index;

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
 * @method  Column  json(string $name)
 *
 * @since  2.1.8
 */
class Schema
{
    /**
     * @var  Column[]
     */
    protected array $columns = [];

    /**
     * @var  Index[]
     */
    protected array $keys = [];

    protected TableManager $table;

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
     * @param  Index  $key
     *
     * @return  Index
     */
    public function addConstraint(Index $key): Index
    {
        $name = $key->constraintName;

        if (!$name) {
            $columns = $key->columns;

            $columns = array_map(
                static fn($col) => explode('(', $col)[0],
                $columns
            );

            $name = sprintf(
                'idx_%s_%s',
                trim($this->table->getName(), '#_'),
                implode('_', $columns)
            );

            $key->constraintName = $name;
        }

        $this->keys[$key->constraintName] = $key;

        return $key;
    }

    public function addIndex(array|string $columns, ?string $name = null): Index
    {
        return $this->addConstraint(new Index(Index::TYPE_INDEX, (array) $columns, $name));
    }

    public function addUniqueKey(array|string $columns, ?string $name = null): Constraint
    {
        // return $this->addKey(new Index(Index::TYPE_UNIQUE, (array) $columns, $name));
    }

    /**
     * addPrimaryKey
     *
     * @param  array|string  $columns
     *
     * @return Index
     */
    public function addPrimaryKey(array|string $columns): Index
    {
        return $this->addConstraint(new Index(Index::TYPE_PRIMARY, (array) $columns, null));
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
     * @return  Index[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * Method to set property indexes
     *
     * @param  Index[]  $keys
     *
     * @return  static  Return self to support chaining.
     */
    public function setKeys(array $keys): static
    {
        $this->keys = $keys;

        return $this;
    }

    public function getDateFormat(): string
    {
        return $this->getTable()->getDb()->getDateFormat();
    }

    public function getNullDate(): string
    {
        return $this->getTable()->getDb()->getNullDate();
    }

    public function __call(string $name, array $args)
    {
        $column = array_shift($args);

        $column = $this->addColumn(new Column($column, $name));

        if ($name === 'primary') {
            $column->dataType('integer')
                ->autoIncrement(true)
                ->primary(true);
        }

        if ($name === 'primaryBigint') {
            $column->dataType('bigint')
                ->autoIncrement(true)
                ->primary(true);
        }

        return $column;
    }
}
