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
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The DatabaseAdapter class.
 */
class DatabaseAdapter
{
    use OptionAccessTrait;

    /**
     * @var AbstractDriver
     */
    protected $driver;

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
                'database' => '',
                'username' => '',
                'password' => '',
                'port' => '',
                'charset' => '',
                'driverOptions' => [],
            ],
            $options
        );
    }

    /**
     * @return AbstractDriver
     */
    public function getDriver(): AbstractDriver
    {
        if (!$this->driver) {
            $this->driver = AbstractDriver::create($this->getOption('driver'), $this);
        }

        return $this->driver;
    }

    /**
     * @return AbstractPlatform
     */
    public function getPlatform(): AbstractPlatform
    {
        return $this->getDriver()->getPlatformName();
    }
}
