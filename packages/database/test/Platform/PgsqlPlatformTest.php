<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use Windwalker\Database\Platform\PgsqlPlatform;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The PgsqlPlatformTest class.
 */
class PgsqlPlatformTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'pgsql';

    protected static $driver = 'pdo_pgsql';

    /**
     * @var PgsqlPlatform
     */
    protected $instance;

    /**
     * @see  PgsqlPlatform::getSchemas
     */
    public function testGetSchemas(): void
    {
        $schemas = $this->instance->getSchemas();

        self::assertContains(
            self::getTestParams()['database'],
            $schemas
        );
    }

    protected function setUp(): void
    {
        $this->instance = static::$db->getPlatform();
    }

    protected function tearDown(): void
    {
    }

    /**
     * @inheritDoc
     */
    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../stub/metadata/' . static::$platform . '.sql');
    }
}
