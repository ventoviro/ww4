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
use Windwalker\Query\Bounded\BindableInterface;

/**
 * Interface StatementInterface
 */
interface StatementInterface extends BindableInterface, \IteratorAggregate
{
    /**
     * execute
     *
     * @param  array|null  $params
     *
     * @return  static
     */
    public function execute(?array $params = null);

    /**
     * Fetch 1 row and move cursor to next position.
     *
     * @param  string  $class
     * @param  array   $args
     *
     * @return  Collection|null
     */
    public function fetch(string $class = Collection::class, array $args = []): ?Collection;

    /**
     * Fetch 1 row and close ths cursor.
     *
     * @param  string  $class
     * @param  array   $args
     *
     * @return  Collection|null
     */
    public function loadOne(string $class = Collection::class, array $args = []): ?Collection;

    /**
     * Fetch all items and close cursor.
     *
     * @param  string  $class
     * @param  array   $args
     *
     * @return  Collection[]|Collection
     */
    public function loadAll(string $class = Collection::class, array $args = []): Collection;

    /**
     * Fetch all column values and close the cursor.
     *
     * @param  int|string  $offset
     *
     * @return  Collection
     */
    public function loadColumn($offset = 0): Collection;

    /**
     * Fetch first cell and close the cursor.
     *
     * @return  string|null
     */
    public function loadResult(): ?string;

    /**
     * Close cursor and free result.
     *
     * @return  static
     */
    public function close();

    /**
     * Count results.
     *
     * @return  int
     */
    public function countAffected(): int;

    /**
     * Get current cursor.
     *
     * @return  mixed
     */
    public function getCursor();

    /**
     * isExecuted
     *
     * @return  bool
     */
    public function isExecuted(): bool;

    /**
     * getIterator
     *
     * @param  string  $class
     * @param  array   $args
     *
     * @return  \Generator
     */
    public function getIterator($class = Collection::class, array $args = []): \Generator;
}
