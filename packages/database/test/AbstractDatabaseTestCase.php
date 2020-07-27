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
use Windwalker\Database\Event\QueryEndEvent;

/**
 * The AbstractDatabaseTestCase class.
 */
abstract class AbstractDatabaseTestCase extends AbstractDatabaseDriverTestCase
{
    protected static string $platform = 'MySQL';

    protected static string $driver = 'pdo_mysql';

    protected static bool $logInited = false;

    protected static ?DatabaseAdapter $db;

    protected static array $lastQueries = [];

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
        static::$lastQueries = [];

        $db = new DatabaseAdapter($params);

        $logFile = __DIR__ . '/../tmp/test-sql.sql';

        if (!static::$logInited) {
            @unlink($logFile);

            static::$logInited = true;
        }

        $db->on(QueryEndEvent::class, function (QueryEndEvent $event) use ($logFile) {
            static::$lastQueries[] = $event['sql'];

            $fp = fopen($logFile, 'ab+');

            fwrite($fp, $event['sql'] . ";\n\n");

            fclose($fp);
        });

        return $db;
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
