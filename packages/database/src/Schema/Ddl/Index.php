<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Ddl;

use Windwalker\Database\Platform\Type\DataType;

/**
 * The Index class.
 */
class Index
{
    use WrapableTrait;

    public string $tableName = '';
    public string $indexName = '';
    public ?string $indexComment = null;
    public bool $isUnique;
    public bool $isPrimary;

    /**
     * @var Column[]
     */
    protected array $columns = [];

    public function tableName(string $tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function name(string $indexName)
    {
        $this->indexName = $indexName;

        return $this;
    }

    public function comment(?string $indexComment)
    {
        $this->indexComment = $indexComment;

        return $this;
    }

    /**
     * columns
     *
     * @param  Column[]|string[]  $columns
     *
     * @return  $this
     */
    public function columns(array $columns)
    {
        $cols = [];

        foreach ($columns as $column) {
            if (!$column instanceof Column) {
                [$colName, $subParts] = DataType::extract($column);

                $column = new Column($colName);

                if ($subParts) {
                    $column->length($subParts);
                }
            } else {
                $column = clone $column;
            }

            $cols[$column->getName()] = $column;
        }

        $this->columns = $cols;

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
