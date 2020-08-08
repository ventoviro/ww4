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
 * The Table class.
 */
class Table
{
    use WrapableTrait;

    public ?string $tableName = null;
    public ?string $tableSchema = null;
    public ?string $tableType = null;
    public ?string $viewDefinition = null;
    public ?string $checkOption = null;
    public ?string $isUpdatable = null;
}
