<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Query\Query;
use Windwalker\Query\Test\Mock\MockConnection;
use function Windwalker\Query\clause;
use function Windwalker\raw;

/**
 * The QueryTest class.
 */
class QueryTest extends TestCase
{
    use QueryTestTrait;

    /**
     * @var Query
     */
    protected $instance;

    /**
     * @param  array   $args
     * @param  array   $addArgs
     * @param  string  $expected
     *
     * @see          Query::select
     *
     * @dataProvider selectProvider
     */
    public function testSelect(array $args, ?array $addArgs, string $expected): void
    {
        $q = $this->instance->select(...$args);

        if ($addArgs !== null) {
            $q = $q->selectAs(...$addArgs);
        }

        self::assertSqlEquals($expected, (string) $q);
    }

    public function selectProvider(): array
    {
        return [
            'array and args' => [
                // args
                [['a', 'b'], 'c', 'd'],
                null,
                // expected
                'SELECT "a", "b", "c", "d"'
            ],
            'AS alias' => [
                // args
                [['a AS aaa', 'b'], 'c AS ccc', 'd'],
                null,
                // expected
                'SELECT "a" AS "aaa", "b", "c" AS "ccc", "d"'
            ],
            'dots' => [
                // args
                [['a.aa AS aaa', 'b.bb'], 'c AS ccc', 'd.dd'],
                null,
                // expected
                'SELECT "a"."aa" AS "aaa", "b"."bb", "c" AS "ccc", "d"."dd"'
            ],
            'raw and clause' => [
                // args
                [[raw('COUNT(*) AS a')], clause('DISTINCT', ['foo AS bar']), 'c AS ccc'],
                null,
                // expected
                'SELECT COUNT(*) AS a, DISTINCT "foo" AS "bar", "c" AS "ccc"'
            ],
            'selectAs' => [
                // args
                ['b AS bbb'],
                [raw('COUNT(*)'), 'count'],
                // expected
                'SELECT "b" AS "bbb", COUNT(*) AS "count"'
            ],
            'raw and selectAs with clause' => [
                // args
                [[raw('COUNT(*) AS a')], 'c AS ccc'],
                [clause('DISTINCT', ['foo AS bar'])],
                // expected
                'SELECT COUNT(*) AS a, "c" AS "ccc", DISTINCT "foo" AS "bar"'
            ],
            'sub query' => [
                // args
                [
                    self::createQuery()
                        ->select('*')
                        ->from('foo')
                        ->alias('foooo'),
                    'bar AS barrr'
                ],
                null,
                // expected
                'SELECT (SELECT * FROM "foo") AS "foooo", "bar" AS "barrr"'
            ],
        ];
    }

    /**
     * @see  Query::from
     */
    public function testFrom(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::clause
     */
    public function testClause(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::escape
     */
    public function testEscape(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::getConnection
     */
    public function testGetConnection(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::setConnection
     */
    public function testSetConnection(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::__get
     */
    public function test__get(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::quoteName
     */
    public function testQuoteName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::getGrammar
     */
    public function testGetGrammar(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::__toString
     */
    public function test__toString(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::quote
     */
    public function testQuote(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Query::alias
     */
    public function testAlias(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public static function createQuery($conn = null): Query
    {
        return new Query($conn ?: new MockConnection());
    }

    protected function setUp(): void
    {
        $this->instance = self::createQuery();
    }

    protected function tearDown(): void
    {
    }
}
