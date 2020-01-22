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
use Windwalker\Database\Driver\Pdo\PdoDriver;

/**
 * The DriverFactory class.
 */
class DriverFactory
{
    protected static $drivers = [
        'pdo' => PdoDriver::class,
    ];

    public static function create(string $name, DatabaseAdapter $db): AbstractDriver
    {
        $names = explode('_', $name);

        $driverClass = self::findDriverClass($names[0]);

        $driver = new $driverClass($db);

        if (($driver instanceof PdoDriver) && isset($names[1])) {
            $driver->setPlatformName($names[1]);
        }

        return $driver;
    }

    public static function findDriverClass(string $name): string
    {
        if (!isset(static::$drivers[strtolower($name)])) {
            throw new \DomainException('Driver: ' . $name . 'not supported');
        }

        return static::$drivers[strtolower($name)];
    }
}
