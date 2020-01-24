<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Mysqli;

use Windwalker\Data\Collection;
use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\AbstractStatement;
use Windwalker\Query\Bounded\ParamType;
use Windwalker\Query\Escaper;

use function Windwalker\collect;

/**
 * The MysqliStatement class.
 */
class MysqliStatement extends AbstractStatement
{
    /**
     * @var \mysqli_stmt
     */
    protected $cursor;

    /**
     * @var \mysqli_result
     */
    protected $result;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var AbstractDriver
     */
    protected $driver;

    /**
     * @inheritDoc
     */
    public function __construct(AbstractDriver $driver, string $query, array $bounded = [])
    {
        $this->bounded = $bounded;
        $this->query = $query;
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(?array $params = null): bool
    {
        if ($params !== null) {
            // Convert array to bounded params
            $params = array_map(
                static function ($param) {
                    return [
                        'value' => $param,
                        'dataType' => ParamType::guessType($param)
                    ];
                },
                $params
            );
        } else {
            $params = $this->bounded;
        }

        $vars = [];

        // Separate params to named and sequenced
        // The named params unable to bind into mysqli, so we replaced them as string value.
        // The sequenced params will be bind into mysqli.
        foreach ($params as $key => $bound) {
            if (!is_int($key)) {
                $vars[$key] = $bound;
                unset($params[$key]);
            }
        }

        $query = Escaper::replaceQueryParams($this->driver, $this->query, $vars);

        /** @var \mysqli $mysqli */
        $mysqli = $this->driver->connect()->get();

        $this->cursor = $stmt = $mysqli->prepare($query);

        if ($params !== []) {
            $types = '';
            $args = [];

            foreach ($params as $param) {
                $type = $param['dataType'] ?? ParamType::guessType($param['value']);

                $types .= ParamType::convertToMysqli($type);
                $args[] = &$param['value'];
            }

            $stmt->bind_param(
                $types,
                ...$args
            );
        }

        $stmt->execute();

        $this->result = $stmt->get_result();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $class = Collection::class, array $args = []): ?Collection
    {
        $this->execute();

        if (!$this->result) {
            return null;
        }

        $row = $this->result->fetch_assoc();

        return $row ? collect($row) : null;
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->cursor->free_result();
        // $this->cursor = null;
        $this->executed = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->cursor->affected_rows;
    }
}
