<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Test\Failer;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Queue\Failer\DatabaseQueueFailer;
use Windwalker\Queue\Failer\PdoQueueFailer;

/**
 * The DatabaseQueueFailerTest class.
 */
class PdoQueueFailerTest extends DatabaseQueueFailerTest
{
    protected function setUp(): void
    {
        $this->instance = new PdoQueueFailer(
            self::$baseConn
        );
    }

    protected function tearDown(): void
    {
    }
}
