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
use Windwalker\Database\Metadata\MetadataInterface;
use Windwalker\Database\Platform\AbstractPlatform;
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
     * @var MetadataInterface
     */
    protected $metadata;

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

    public function replacePrefix(string $query): string
    {
        return $query;
    }
}
