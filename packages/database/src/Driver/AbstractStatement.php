<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Windwalker\Data\Collection;

use Windwalker\Database\Exception\StatementException;
use Windwalker\Database\Iterator\StatementIterator;
use Windwalker\Query\Bounded\BindableTrait;
use function Windwalker\collect;
use function Windwalker\tap;
use function Windwalker\where;

/**
 * The AbstractStatement class.
 */
abstract class AbstractStatement implements StatementInterface
{
    use BindableTrait;

    /**
     * @var mixed|resource
     */
    protected $cursor;

    /**
     * @var bool
     */
    protected $executed = false;

    /**
     * @inheritDoc
     */
    public function getIterator($class = Collection::class, array $args = []): \Generator
    {
        $gen = function () use ($class, $args) {
            $this->execute();

            while (($row = $this->fetch($class, $args)) !== null) {
                yield $row;
            }
        };

        return $gen();
    }

    /**
     * execute
     *
     * @param  array|null  $params
     *
     * @return  static
     */
    public function execute(?array $params = null)
    {
        if ($this->executed) {
            return $this;
        }

        $r = $this->doExecute($params);

        if (!$r) {
            throw new StatementException('Execute query statement failed.');
        }

        $this->executed = true;

        return $this;
    }

    /**
     * Execute query by driver.
     *
     * @param  array|null  $params
     *
     * @return  bool
     */
    abstract protected function doExecute(?array $params = null): bool;

    /**
     * @inheritDoc
     */
    public function loadOne(string $class = Collection::class, array $args = []): ?Collection
    {
        return tap($this->fetch($class, $args), function () {
            $this->close();
        });
    }

    /**
     * @inheritDoc
     */
    public function loadAll(string $class = Collection::class, array $args = []): Collection
    {
        $this->execute();

        $array = [];

        // Get all of the rows from the result set.
        while ($row = $this->fetch($class, $args)) {
            $array[] = $row;
        }

        $items = collect($array);

        $this->close();

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function loadColumn($offset = 0): Collection
    {
        return $this->loadAll()
            ->mapProxy()
            ->values()
            ->column($offset);
    }

    /**
     * @inheritDoc
     */
    public function loadResult(): ?string
    {
        $assoc = $this->loadOne();

        if ($assoc === null) {
            return $assoc;
        }

        return $assoc->first();
    }

    /**
     * getInnerStatement
     *
     * @return  mixed|resource
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * isExecuted
     *
     * @return  bool
     */
    public function isExecuted(): bool
    {
        return $this->executed;
    }
}
