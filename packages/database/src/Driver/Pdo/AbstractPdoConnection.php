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
     * isSupported
     *
     * @return  bool
     */
    public static function isSupported(): bool
    {
        if (!class_exists(\PDO::class)) {
            return false;
        }

        return in_array(strtolower(static::$dbtype), \PDO::getAvailableDrivers(), true);
    }

    /**
     * getDsn
     *
     * @param  array  $options
     *
     * @return  string
     */
    public function getDsn(array $options): string
    {
        return DsnHelper::build($options, static::$dbtype);
    }

    /**
     * doConnect
     *
     * @param  array  $options
     *
     * @return  \PDO
     */
    protected function doConnect(array $options)
    {
        return new \PDO(
            $options['dsn'],
            $options['username'] ?? null,
            $options['password'] ?? null,
            $options['pdo_attributes'] ?? []
        );
    }

    /**
     * @return \PDO|null
     */
    public function getConnection(): ?\PDO
    {
        return parent::getConnection();
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
