<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Mysqli;

use Windwalker\Data\Collection;
use Windwalker\Database\Driver\AbstractStatement;

/**
 * The MysqliStatement class.
 */
class MysqliStatement extends AbstractStatement
{
    /**
     * @inheritDoc
     */
    protected function doExecute(?array $params = null): bool
    {
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $class = Collection::class, array $args = []): ?Collection
    {
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
    }
}
