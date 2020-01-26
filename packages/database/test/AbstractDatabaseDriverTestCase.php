<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test;

use Asika\SqlSplitter\SqlSplitter;
use PHPUnit\Framework\TestCase;
use Windwalker\Database\Driver\Pdo\AbstractPdoConnection;
use Windwalker\Database\Driver\Pdo\DsnHelper;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Query\Test\QueryTestTrait;

/**
 * The AbstractDatabaseTestCase class.
 */
abstract class AbstractDatabaseDriverTestCase extends TestCase
{
    use QueryTestTrait;

    protected static $platform = '';

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
        parent::setUpBeforeClass();

        $params = static::getTestParams();

        if (!$params) {
            self::markTestSkipped('DSN of ' . static::$platform . ' not available for test case: ' . static::class);
        }

        /** @var AbstractPdoConnection $connClass */
        $connClass = 'Windwalker\Database\Driver\Pdo\Pdo' . ucfirst(static::$platform) . 'Connection';

        if (!class_exists($connClass) || !is_subclass_of($connClass, AbstractPdoConnection::class)) {
            throw new \LogicException(
                sprintf(
                    '%s should exists and extends %s',
                    $connClass,
                    AbstractPdoConnection::class
                )
            );
        }

        if (static::$platform !== 'sqlite') {
            static::$dbname = $params['database'];
            unset($params['database']);

            $pdo = static::createBaseConnect($params, $connClass);

            // TODO: Use faster way to refresh test DB and tables
            if (static::$platform === 'sqlsrv') {
                $pdo->exec(
                    sprintf(
                        'ALTER DATABASE %s SET SINGLE_USER WITH ROLLBACK IMMEDIATE',
                        static::$dbname
                    )
                );
            }

            $pdo->exec('DROP DATABASE ' . static::qn(static::$dbname));
            $pdo->exec('CREATE DATABASE ' . static::qn(static::$dbname));

            // Disconnect.
            $pdo = null;

            $params['database'] = static::$dbname;
        } else {
            static::$dbname = $params['database'];

            @unlink(static::$dbname);
        }

        static::$baseConn = static::createBaseConnect($params, $connClass);

        static::setupDatabase();
    }

    protected static function createBaseConnect(array $params, string $connClass): \PDO
    {
        $dsn = $connClass::getParameters($params)['dsn'];

        return new \PDO(
            $dsn,
            $params['username'] ?? null,
            $params['password'] ?? null
        );
    }

    /**
     * setupDatabase
     *
     * @return  void
     */
    abstract protected static function setupDatabase(): void;

    /**
     * importFromFile
     *
     * @param  string  $file
     *
     * @return  void
     */
    protected static function importFromFile(string $file): void
    {
        if (!is_file($file)) {
            throw new \RuntimeException('File not found: ' . $file);
        }

        self::importIterator(SqlSplitter::splitFromFile($file));
    }

    /**
     * importIterator
     *
     * @param  iterable  $queries
     *
     * @return  void
     */
    protected static function importIterator(iterable $queries): void
    {
        foreach ($queries as $query) {
            if (trim($query) === '') {
                continue;
            }

            static::$baseConn->exec($query);
        }
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        // static::$baseConn->exec('DROP DATABASE ' . static::qn(static::$dbname));

        static::$baseConn = null;
    }

    /**
     * getTestParams
     *
     * @return  array
     */
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

    /**
     * getGrammar
     *
     * @return  Grammar
     */
    public static function getGrammar(): Grammar
    {
        return Grammar::create(static::$platform);
    }

    /**
     * quote
     *
     * @param  string  $text
     *
     * @return  string
     */
    protected static function qn(string $text): string
    {
        return static::getGrammar()->quoteName($text);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::$baseConn = null;
    }
}
