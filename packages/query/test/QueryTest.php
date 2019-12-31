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
     * @param  array        $args
     * @param  array        $addArgs
     * @param  string       $expected
     * @param  string|null  $subQueryAlias
     * @param  string|null  $modifiedSql
     *
     * @see          Query::select
     *
     * @dataProvider selectProvider
     */
    public function testSelect(
        array $args,
        ?array $addArgs,
        string $expected,
        ?string $subQueryAlias = null,
        ?string $modifiedSql = null
    ): void {
        $q = $this->instance->select(...$args);

        if ($addArgs !== null) {
            $q = $q->selectAs(...$addArgs);
        }

        self::assertSqlEquals($expected, (string) $q);

        if ($subQueryAlias) {
            $sub = $q->getSubQuery($subQueryAlias);

            self::assertInstanceOf(Query::class, $sub);

            $sub->select('newcol');

            self::assertEquals($modifiedSql, (string) $q);
        }
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
                'SELECT (SELECT * FROM "foo") AS "foooo", "bar" AS "barrr"',
                // Sub query
                'foooo',
                'SELECT (SELECT *, "newcol" FROM "foo") AS "foooo", "bar" AS "barrr"'
            ],
        ];
    }

    /**
     * @param  mixed        $tables
     * @param  string|null  $alias
     * @param  string       $expected
     *
     * @see          Query::from
     *
     * @dataProvider fromProvider
     */
    public function testFrom($tables, ?string $alias, string $expected): void
    {
        $q = $this->instance
            ->select('*')
            ->from($tables, $alias);

        self::assertSqlEquals($expected, (string) $q);
    }

    public function fromProvider(): array
    {
        return [
            'Simple from' => [
                'foo',
                null,
                'SELECT * FROM "foo"'
            ],
            'Simple from as' => [
                'a.foo',
                'foo',
                'SELECT * FROM "a"."foo" AS "foo"'
            ],
            'Multiple tables' => [
                ['f' => 'foo', 'b' => 'bar', 'y' => 'yoo'],
                'nouse',
                'SELECT * FROM "foo" AS "f", "bar" AS "b", "yoo" AS "y"'
            ],
            'single sub query' => [
                self::createQuery()
                    ->select('*')
                    ->from('flower'),
                null,
                'SELECT * FROM (SELECT * FROM "flower")'
            ],
            'single sub query as' => [
                self::createQuery()
                    ->select('*')
                    ->from('flower'),
                'f',
                'SELECT * FROM (SELECT * FROM "flower") AS "f"'
            ],
            'single sub query with self-alias' => [
                self::createQuery()
                    ->select('*')
                    ->from('flower')
                    ->alias('fl'),
                null,
                'SELECT * FROM (SELECT * FROM "flower") AS "fl"'
            ],
            'single sub query with self-alias and as' => [
                self::createQuery()
                    ->select('*')
                    ->from('flower')
                    ->alias('fl'),
                'f',
                'SELECT * FROM (SELECT * FROM "flower") AS "f"'
            ],
            'Multiple tables with sub query' => [
                [
                    'a' => 'ace',
                    'f' => self::createQuery()
                        ->select('*')
                        ->from('flower')
                        ->alias('fl_nouse')
                ],
                'nouse',
                'SELECT * FROM "ace" AS "a", (SELECT * FROM "flower") AS "f"'
            ],
        ];
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
