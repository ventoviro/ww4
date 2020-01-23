<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Data\Collection;
use Windwalker\Database\Driver\AbstractStatement;
use Windwalker\Query\Bounded\ParamType;

/**
 * The PdoStatement class.
 *
 * @method \PDOStatement getInnerStatement()
 */
class PdoStatement extends AbstractStatement
{
    /**
     * @var \PDOStatement
     */
    protected $cursor;

    /**
     * @var bool
     */
    protected $executed = false;

    /**
     * PdoStatement constructor.
     *
     * @param  \PDOStatement  $stmt
     * @param  array          $bounded
     */
    public function __construct(\PDOStatement $stmt, array $bounded = [])
    {
        $this->cursor = $stmt;

        foreach ($bounded as $key => $bound) {
            $key = is_int($key) ? $key + 1 : $key;

            $this->bindParam(
                $key,
                $bound['value'],
                $bound['dataType'] ?? null,
                $bound['length'] ?? 0,
                $bound['driverOptions'] ?? null
            );
        }
    }

    public function execute(?array $params = null): bool
    {
        if ($this->executed) {
            return true;
        }

        $r = $this->cursor->execute($params);

        $this->executed = true;

        return $r;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne(string $class = Collection::class, array $args = []): ?Collection
    {
        $this->execute();

        $item = $this->cursor->fetch(\PDO::FETCH_ASSOC);

        return $item !== false ? \Windwalker\collect($item) : null;
    }

    /**
     * @inheritDoc
     */
    public function bindParam($key = null, &$value = null, $dataType = null, int $length = 0, $driverOptions = null)
    {
        $dataType = $dataType ?? ParamType::guessType($value);

        $this->cursor->bindParam(
            $key,
            $value,
            ParamType::convertToPDO($dataType),
            $length,
            $driverOptions
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bind($key = null, $value = null, $dataType = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->bind($k, $v);
            }

            return $this;
        }

        $this->bindParam($key, $value, $dataType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        $this->executed = false;

        return $this->cursor->closeCursor();
    }
}
