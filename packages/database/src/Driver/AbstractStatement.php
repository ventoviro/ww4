<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Psr\EventDispatcher\EventDispatcherInterface;
use Windwalker\Data\Collection;
use Windwalker\Database\Event\QueryEndEvent;
use Windwalker\Database\Event\QueryFailedEvent;
use Windwalker\Database\Event\QueryStartEvent;
use Windwalker\Database\Exception\StatementException;
use Windwalker\Event\EventEmitter;
use Windwalker\Event\EventListenableTrait;
use Windwalker\Query\Bounded\BindableTrait;

use function Windwalker\collect;
use function Windwalker\tap;

/**
 * The AbstractStatement class.
 */
abstract class AbstractStatement implements StatementInterface
{
    use BindableTrait;
    use EventListenableTrait;

    /**
     * @var mixed|resource
     */
    protected $cursor;

    /**
     * @var bool
     */
    protected $executed = false;

    /**
     * @var EventEmitter
     */
    protected $dispatcher;

    /**
     * AbstractStatement constructor.
     *
     * @param  mixed|resource  $cursor
     */
    public function __construct($cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @inheritDoc
     */
    public function getIterator($class = Collection::class, array $args = []): \Generator
    {
        $gen = function () use ($class, $args) {
            $this->execute();

            while (($row = $this->fetch($args)) !== null) {
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

        $statement = $this;
        $dispatcher = $this->getDispatcher();

        $dispatcher->emit(QueryStartEvent::class, compact('params'));

        try {
            $result = $this->doExecute($params);

            if (!$result) {
                throw new StatementException('Execute query statement failed.');
            }
        } catch (\RuntimeException $exception) {
            $statement->close();
            $event = $dispatcher->emit(QueryFailedEvent::class, compact('exception'));

            throw $event->getException();
        }

        $dispatcher->emit(QueryEndEvent::class, compact('result'));

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
    public function get(array $args = []): ?Collection
    {
        return tap(
            $this->fetch($args),
            function () {
                $this->close();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function all(array $args = []): Collection
    {
        $this->execute();

        $array = [];

        // Get all of the rows from the result set.
        while ($row = $this->fetch($args)) {
            $array[] = $row;
        }

        $items = collect($array);

        $this->close();

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function loadColumn(int|string $offset = 0): Collection
    {
        return $this->all()
            ->mapProxy()
            ->values()
            ->column($offset);
    }

    /**
     * @inheritDoc
     */
    public function result(): ?string
    {
        $assoc = $this->get();

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

    /**
     * @inheritDoc
     */
    public function addDispatcherDealer(EventDispatcherInterface $dispatcher): void
    {
        $this->getDispatcher()->registerDealer($dispatcher);
    }
}
