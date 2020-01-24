<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Query\Query;

/**
 * The AbstractDriver class.
 */
abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $platformName = '';

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * AbstractPlatform constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    /**
     * handleQuery
     *
     * @param  string|Query  $query
     * @param  array         $bounded
     *
     * @return  string
     */
    protected function handleQuery($query, ?array &$bounded = []): string
    {
        if ($query instanceof Query) {
            return $query->render(false, $bounded);
        }

        $bounded = $bounded ?? [];

        return (string) $query;
    }

    /**
     * connect
     *
     * @return  ConnectionInterface
     */
    public function connect(): ConnectionInterface
    {
        $conn = $this->getConnection();

        if ($conn->isConnected()) {
            return $conn;
        }

        $conn->connect();

        return $conn;
    }

    /**
     * disconnect
     *
     * @return  mixed
     */
    public function disconnect()
    {
        return $this->getConnection()->disconnect();
    }

    /**
     * @inheritDoc
     */
    public function execute($query, ?array $params = null): StatementInterface
    {
        return $this->prepare($query)->execute($params);
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        return $this->platformName;
    }

    public function getPlatform(): AbstractPlatform
    {
        if (!$this->platform) {
            $this->platform = AbstractPlatform::create($this->platformName, $this->db);
        }

        return $this->platform;
    }

    /**
     * @param  string  $platformName
     *
     * @return  static  Return self to support chaining.
     */
    public function setPlatformName(string $platformName)
    {
        $this->platformName = $platformName;

        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        if (!$this->connection) {
            $this->connection = $this->createConnection();
        }

        return $this->connection;
    }

    public function createConnection(): ConnectionInterface
    {
        $class = $this->getConnectionClass();

        return new $class($this->db->getOptions());
    }

    protected function getConnectionClass(): string
    {
        $class = __NAMESPACE__ . '\%s\%sConnection';

        return sprintf(
            $class,
            ucfirst($this->name),
            ucfirst($this->name)
        );
    }

    /**
     * @param  ConnectionInterface  $connection
     *
     * @return  static  Return self to support chaining.
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
