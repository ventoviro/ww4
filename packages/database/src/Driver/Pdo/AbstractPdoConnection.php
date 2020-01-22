<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Database\Driver\AbstractConnection;

/**
 * The PdoConnection class.
 */
abstract class AbstractPdoConnection extends AbstractConnection
{
    /**
     * @var string
     */
    protected static $dbtype = '';

    /**
     * getDsn
     *
     * @param  array  $options
     *
     * @return  string
     */
    public function getDsn(array $options): string
    {
        $params = [];

        foreach ($this->getDsnParameters($options) as $key => $value) {
            $params[] = $key . '=' . $value;
        }

        return static::$dbtype . ':' . implode(';', $params);
    }

    abstract public function getDsnParameters(array $options): array;

    /**
     * @inheritDoc
     */
    public function connect()
    {
        if ($this->connection) {
            return $this->connection;
        }

        $pdo = new \PDO($this->getDsn($this->options));

        foreach ($this->options['pdo_attributes'] ?? [] as $key => $value) {
            $pdo->setAttribute($key, $value);
        }

        $this->connection = $pdo;

        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * @return string
     */
    public static function getDbType(): string
    {
        return self::$dbtype;
    }
}
