<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use Windwalker\Database\Platform\MySQLPlatform;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The AbstractPlatformTest class.
 */
abstract class AbstractPlatformTest extends AbstractDatabaseTestCase
{
    /**
     * @var MySQLPlatform
     */
    protected $instance;

    protected function setUp(): void
    {
        $this->instance = static::$db->getDriver()->getPlatform();
    }

    /**
     * Method to test start().
     *
     * @return void
     */
    // public function testTransactionRollback()
    // {
    //     $table = '#__flower';
    //
    //     $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('A', '', ''), ('B', '', ''), ('C', '', '')";
    //
    //     $this->instance->transactionStart();
    //
    //     static::$db->execute($sql);
    //
    //     $this->instance->transactionRollback();
    //
    //     $result = static::$db->prepare('SELECT title FROM #__flower WHERE title = \'A\'')->loadResult();
    //
    //     $this->assertNull($result);
    // }

    /**
     * Method to test start().
     *
     * @return void
     */
    public function testTransactionCommit()
    {
        $table = '#__flower';

        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('A', '', ''), ('B', '', ''), ('C', '', '')";

        $tran = $this->instance->transactionStart();

        static::$db->execute($sql);

        $this->instance->transactionCommit();

        $result = static::$db->prepare('SELECT title FROM #__flower WHERE title = \'A\'')->loadResult();

        $this->assertEquals('A', $result);
    }

    /**
     * testTransactionNested
     *
     * @return  void
     */
    public function testTransactionNested()
    {
        $table = '#__flower';

        // Level 1
        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('D', '', '')";

        $tran = $this->instance->transactionStart();

        static::$db->execute($sql);

        // Level 2
        $sql = "INSERT INTO {$table} (title, meaning, params) VALUES ('E', '', '')";

        $tran = $this->instance->transactionStart();

        static::$db->execute($sql);

        $this->instance->transactionRollback();
        $this->instance->transactionCommit();

        $result = static::$db->prepare('SELECT title FROM #__flower WHERE title = \'D\'')->loadResult();
        $this->assertEquals('D', $result);

        $result2 = static::$db->prepare('SELECT title FROM #__flower WHERE title = \'E\'')->loadResult();
        $this->assertNotEquals('E', $result2);
    }

    protected function tearDown(): void
    {
    }

    /**
     * @inheritDoc
     */
    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../stub/' . static::$platform . '.sql');
    }
}