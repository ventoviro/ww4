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
    protected static $name = 'pdo';

    /**
     * @var string
     */
    protected static $dbtype = '';

    protected static $defaultAttributes = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => true,
        \PDO::ATTR_STRINGIFY_FETCHES => false
    ];

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
    public static function getDsn(array $options): string
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
        $attrs = array_replace(
            static::$defaultAttributes,
            $options['driverOptions'] ?? []
        );

        return new \PDO(
            $options['dsn'],
            $options['username'] ?? null,
            $options['password'] ?? null,
            $attrs
        );
    }

    /**
     * @return \PDO|null
     */
    public function get(): ?\PDO
    {
        return parent::get();
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
