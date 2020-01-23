<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Database\Driver\StatementInterface;

/**
 * The PdoStatement class.
 */
class PdoStatement implements StatementInterface
{
    /**
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * PdoStatement constructor.
     *
     * @param  \PDOStatement  $stmt
     * @param  array          $bounded
     */
    public function __construct(\PDOStatement $stmt, array $bounded = [])
    {
        $this->stmt = $stmt;

        foreach ($bounded as $key => $bound) {
            $stmt->bindParam($key, $bound['value']);
        }
    }
}
