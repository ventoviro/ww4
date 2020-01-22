<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Driver\Pdo\DsnHelper;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Query\Test\QueryTestTrait;

/**
 * The AbstractDatabaseTestCase class.
 */
abstract class AbstractDatabaseTestCase extends TestCase
{
    use QueryTestTrait;

    protected static $platform = '';

    protected static $driver = '';

    protected static $dbname = '';

    /**
     * @var \PDO
     */
    protected static $baseConn;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        static::setupDatabase();

        parent::setUpBeforeClass();

        $params = static::getTestParams();

        if (!$params) {
            self::markTestSkipped('DSN of ' . static::$platform . ' not available.');
        }

        static::$dbname = $params['database'];
        $user = $params['username'];
        $pass = $params['password'];
        unset(
            $params['database'],
            $params['username'],
            $params['password']
        );

        static::$baseConn = new \PDO(
            DsnHelper::build($params, static::$platform),
            $user,
            $pass
        );
        static::$baseConn->exec('CREATE DATABASE ' . static::qn(static::$dbname));
    }

    abstract protected static function setupDatabase(): void;

    public function __destruct()
    {
        // static::$baseConn->exec('DROP DATABASE ' . static::qn(static::$dbname));
    }

    protected static function getTestParams(): array
    {
        $const = 'WINDWALKER_TEST_DB_DSN_' . strtoupper(static::$platform);

        // First let's look to see if we have a DSN defined or in the environment variables.
        if (defined($const) || getenv($const)) {
            $dsn = (defined($const) ? constant($const) : getenv($const));

            return DsnHelper::extract($dsn);
        }

        return [];
    }

    public static function getGrammar(): Grammar
    {
        return Grammar::create(static::$platform);
    }

    /**
     * quote
     *
     * @param string $text
     *
     * @return  string
     */
    protected static function qn(string $text): string
    {
        return static::getGrammar()->quoteName($text);
    }
}
