<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

/**
 * Interface TransactionDriverInterface
 */
interface TransactionDriverInterface
{
    /**
     * start
     *
     * @return  bool
     */
    public function transactionStart(): bool;

    /**
     * commit
     *
     * @return  bool
     */
    public function transactionCommit(): bool;

    /**
     * rollback
     *
     * @return  bool
     */
    public function transactionRollback(): bool;
}
