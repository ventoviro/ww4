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

use Windwalker\Query\Bounded\BindableTrait;
use function Windwalker\collect;

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
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    protected function handle(\Closure $callback)
    {
        $this->execute();

        $result = $callback();

        $this->close();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): Collection
    {
        $this->execute();

        $array = [];

        // Get all of the rows from the result set.
        while ($row = $this->fetchOne()) {
            $array[] = $row;
        }

        $items = collect($array);

        $this->close();

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn($offset = 0): Collection
    {
        return $this->fetchAll()
            ->mapProxy()
            ->values()
            ->column($offset);
    }

    /**
     * @inheritDoc
     */
    public function fetchResult(): ?string
    {
        $assoc = $this->fetchOne();

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
    public function getInnerStatement()
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
