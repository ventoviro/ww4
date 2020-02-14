<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\DriverFactory;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Database\Schema\AbstractSchemaManager;
use Windwalker\Event\EventAttachableInterface;
use Windwalker\Event\ListenableTrait;
use Windwalker\Query\Query;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The DatabaseAdapter class.
 */
class DatabaseAdapter implements EventAttachableInterface
{
    use OptionAccessTrait;
    use ListenableTrait;

    /**
     * @var AbstractDriver
     */
    protected $driver;

    /**
     * @var Query|string
     */
    protected $query;

    /**
     * DatabaseAdapter constructor.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->prepareOptions(
            [
                'driver' => '',
                'host' => 'localhost',
                'database' => null,
                'username' => null,
                'password' => null,
                'port' => null,
                'prefix' => null,
                'charset' => null,
                'driverOptions' => [],
            ],
            $options
        );
    }

    public function prepare($query, array $options = []): StatementInterface
    {
        $this->query = $query;

        return $this->getDriver()->prepare($query, $options);
    }

    public function execute($query, ?array $params = null): StatementInterface
    {
        $this->query = $query;

        return $this->getDriver()->execute($query, $params);
    }

    /**
     * getQuery
     *
     * @param  bool  $new
     *
     * @return  string|Query
     */
    public function getQuery(bool $new = false)
    {
        if ($new) {
            return $this->getPlatform()->createQuery();
        }

        return $this->query;
    }

    /**
     * quoteName
     *
     * @param array|string $value
     *
     * @return  array|string
     */
    public function quoteName($value)
    {
        return $this->getQuery(true)->quoteName($value);
    }

    /**
     * @return AbstractDriver
     */
    public function getDriver(): AbstractDriver
    {
        if (!$this->driver) {
            $this->driver = DriverFactory::create($this->getOption('driver'), $this);
        }

        return $this->driver;
    }

    /**
     * @return AbstractPlatform
     */
    public function getPlatform(): AbstractPlatform
    {
        return $this->getDriver()->getPlatform();
    }

    /**
     * @return  AbstractSchemaManager
     */
    public function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getDriver()->getSchemaManager();
    }

    public function replacePrefix(string $query, string $prefix = '#__'): string
    {
        return $this->getDriver()->replacePrefix($query, $prefix);
    }

    /**
     * transaction
     *
     * @param  callable  $callback
     * @param  bool      $autoCommit
     * @param  bool      $enabled
     *
     * @return  static
     *
     * @throws \Throwable
     */
    public function transaction(callable $callback, bool $autoCommit = true, bool $enabled = true)
    {
        $this->getPlatform()->transaction($callback, $autoCommit, $enabled);

        return $this;
    }
}
