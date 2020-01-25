<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Query\Escaper;

/**
 * The PdoDriver class.
 */
class PdoDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected static $name = 'pdo';

    /**
     * @var string
     */
    protected $platformName = 'odbc';

    protected function getConnectionClass(): string
    {
        $class = __NAMESPACE__ . '\Pdo%sConnection';

        return sprintf(
            $class,
            ucfirst($this->platformName)
        );
    }

    /**
     * @inheritDoc
     */
    public function doPrepare(string $query, array $bounded = [], array $options = []): StatementInterface
    {
        /** @var \PDO $pdo */
        $pdo = $this->connect()->get();

        return new PdoStatement(
            static function () use ($pdo, $query, $options, $bounded) {
                return [
                    $pdo->prepare($query, $options),
                    static function (PdoStatement $stmt) use ($bounded) {
                        foreach ($bounded as $key => $bound) {
                            $key = is_int($key) ? $key + 1 : $key;

                            $stmt->bindParam(
                                $key,
                                $bound['value'],
                                $bound['dataType'] ?? null,
                                $bound['length'] ?? 0,
                                $bound['driverOptions'] ?? null
                            );
                        }
                    }
                ];
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(?string $sequence = null): ?string
    {
        /** @var \PDO $pdo */
        $pdo = $this->connect()->get();

        return $pdo->lastInsertId($sequence);
    }

    /**
     * @inheritDoc
     */
    public function quote(string $value): string
    {
        /** @var \PDO $pdo */
        $pdo = $this->connect()->get();

        return $pdo->quote($value);
    }

    /**
     * @inheritDoc
     */
    public function escape(string $value): string
    {
        return Escaper::stripQuote($this->quote($value));
    }
}
