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
use Windwalker\Event\ListenableTrait;
use Windwalker\Query\Bounded\BindableTrait;

use function Windwalker\collect;
use function Windwalker\tap;

/**
 * The AbstractStatement class.
 */
abstract class AbstractStatement implements StatementInterface
{
    use BindableTrait;
    use ListenableTrait;

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

        $statement = $this;
        $dispatcher = $this->getDispatcher();

        $dispatcher->emit(new QueryStartEvent(compact('params')));

        try {
            $result = $this->doExecute($params);

            if (!$result) {
                throw new StatementException('Execute query statement failed.');
            }
        } catch (\RuntimeException $exception) {
            $statement->close();
            $event = $dispatcher->emit(new QueryFailedEvent(compact('exception')));

            throw $event['exception'];
        }

        $dispatcher->emit(new QueryEndEvent(compact('result')));

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
        return tap(
            $this->fetch($class, $args),
            function () {
                $this->close();
            }
        );
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

    public static function replaceStatement(string $sql, string $symbol = '?', array $params = []): array
    {
        $values = [];
        $i      = 0;
        $s      = 1;

        $sql = (string) preg_replace_callback(
            '/(:[\w_]+|\?)/',
            function ($matched) use (
                &$values,
                &$i,
                &$s,
                $symbol,
                $params
            ) {
                $name = $matched[0];

                if ($name === '?') {
                    $values[] = $params[$i];
                    $i++;
                } else {
                    if (!array_key_exists($name, $params) && !array_key_exists(ltrim($name, ':'), $params)) {
                        return $name;
                    }

                    $values[] = $params[$name] ?? $params[ltrim($name, ':')] ?? null;
                }

                if (strpos($symbol, '%d') !== false) {
                    $symbol = str_replace('%d', $s, $symbol);
                    $s++;
                }

                return $symbol;
            },
            $sql
        );

        return [$sql, $values];
    }

    /**
     * @inheritDoc
     */
    public function addDispatcherDealer(EventDispatcherInterface $dispatcher): void
    {
        $this->getDispatcher()->registerDealer($dispatcher);
    }
}
