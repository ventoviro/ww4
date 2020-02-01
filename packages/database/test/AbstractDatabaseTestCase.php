<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test;

use Windwalker\Database\DatabaseAdapter;

/**
 * The AbstractDatabaseTestCase class.
 */
abstract class AbstractDatabaseTestCase extends AbstractDatabaseDriverTestCase
{
    protected static $platform = 'MySQL';

    protected static $driver = 'pdo_mysql';

    /**
     * @var DatabaseAdapter
     */
    protected static $db;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$db = self::createAdapter();
    }

    protected static function createAdapter(?array $params = null): DatabaseAdapter
    {
        $params           = $params ?? self::getTestParams();
        $params['driver'] = static::$driver;

        return new DatabaseAdapter($params);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::$db->getDriver()->disconnect();
        static::$db = null;
    }
}
