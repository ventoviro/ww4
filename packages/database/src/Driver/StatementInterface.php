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
    public function execute(?array $params = null): bool;

    /**
     * fetchOne
     *
     * @return  Collection|null
     */
    public function fetchOne(): ?Collection;

    /**
     * fetchObjectList
     *
     * @return  Collection[]|Collection
     */
    public function fetchAll(): Collection;

    /**
     * fetchColumn
     *
     * @param  int|string  $offset
     *
     * @return  Collection
     */
    public function fetchColumn($offset = 0): Collection;

    /**
     * fetchResult
     *
     * @return  string|null
     */
    public function fetchResult(): ?string;

    /**
     * Close statement and free result.
     *
     * @return  bool
     */
    public function close(): bool;

    /**
     * getInnerStatement
     *
     * @return  mixed
     */
    public function getInnerStatement();

    /**
     * isExecuted
     *
     * @return  bool
     */
    public function isExecuted(): bool;
}
