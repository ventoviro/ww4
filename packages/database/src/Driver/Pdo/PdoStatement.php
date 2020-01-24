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

use function Windwalker\collect;

/**
 * The PdoStatement class.
 *
 * @method \PDOStatement getCursor()
 */
class PdoStatement extends AbstractStatement
{
    /**
     * @var \PDOStatement
     */
    protected $cursor;

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

    /**
     * @inheritDoc
     */
    protected function doExecute(?array $params = null): bool
    {
        return (bool) $this->cursor->execute($params);
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $class = Collection::class, array $args = []): ?Collection
    {
        $this->execute();

        $item = $this->cursor->fetch(\PDO::FETCH_ASSOC);

        return $item !== false ? collect($item) : null;
    }

    /**
     * @inheritDoc
     */
    public function bindParam($key = null, &$value = null, $dataType = null, int $length = 0, $driverOptions = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->bindParam($k, $v);
            }

            return $this;
        }

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
    public function close()
    {
        $this->cursor->closeCursor();

        $this->executed = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->cursor->rowCount();
    }
}
