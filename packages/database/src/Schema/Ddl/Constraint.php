<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Ddl;

/**
 * The Constraint class.
 */
class Constraint
{
    use WrapableTrait;

    public const TYPE_PRIMARY_KEY = 'PRIMARY KEY';
    public const TYPE_UNIQUE = 'UNIQUE';
    public const TYPE_FOREIGN_KEY = 'FOREIGN KEY';
    public const TYPE_CHECK = 'CHECK';

    public string $constraintName = '';
    public string $constraintType = '';
    public string $tableName = '';
    public ?string $referencedTableSchema = null;
    public ?string $referencedTableName = null;
    public ?string $matchOption = null;
    public ?string $updateRule = null;
    public ?string $deleteRule = null;
    /**
     * @var Column[]
     */
    public array $columns = [];

    /**
     * @var Column[]
     */
    public array $referencedColumns = [];

    /**
     * Constraint constructor.
     *
     * @param  string  $constraintType
     * @param  string  $constraintName
     * @param  string  $tableName
     */
    public function __construct(string $constraintType, string $constraintName, string $tableName)
    {
        $this->constraintName = $constraintName;
        $this->tableName      = $tableName;
        $this->constraintType = $constraintType;
    }
}
