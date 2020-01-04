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
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;
use Windwalker\Query\Test\Mock\MockEscaper;
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
            'sub query with Closure' => [
                // args
                [
                    static function (Query $query) {
                        $query->select('*')
                            ->from('foo')
                            ->alias('foooo');
                    },
                    'bar AS barrr'
                ],
                null,
                // expected
                'SELECT (SELECT * FROM "foo") AS "foooo", "bar" AS "barrr"',
            ],
            // TODO: Move to new test
            'sub query modified' => [
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
     * testAs
     *
     * @param  string  $expt
     * @param  mixed   $value
     * @param  mixed   $alias
     * @param  bool    $isColumn
     *
     * @return  void
     *
     * @dataProvider asProvider
     */
    public function testAs(string $expt, $value, $alias, bool $isColumn = true): void
    {
        self::assertEquals($expt, (string) $this->instance->as($value, $alias, $isColumn));
    }

    public function asProvider(): array
    {
        return [
            'Simple quote name' => [
                '"foo"',
                'foo',
                null,
                true
            ],
            'Column with as' => [
                '"foo" AS "f"',
                'foo',
                'f',
                true
            ],
            'String value' => [
                '\'foo\'',
                'foo',
                'f',
                false
            ],
            'Sub query with as' => [
                '(SELECT * FROM "bar") AS "bar"',
                self::createQuery()
                    ->select('*')
                    ->from('bar'),
                'bar',
                true
            ],
            'Sub query contains as but override' => [
                '(SELECT * FROM "bar") AS "bar"',
                self::createQuery()
                    ->select('*')
                    ->from('bar')
                    ->alias('b'),
                'bar',
                true
            ],
            'Sub query contains alias' => [
                '(SELECT * FROM "bar") AS "b"',
                self::createQuery()
                    ->select('*')
                    ->from('bar')
                    ->alias('b'),
                null,
                true
            ],
            'Sub query contains alias but force ignore' => [
                '(SELECT * FROM "bar")',
                self::createQuery()
                    ->select('*')
                    ->from('bar')
                    ->alias('b'),
                false,
                true
            ],
            'Sub query as value' => [
                '(SELECT * FROM "bar")',
                self::createQuery()
                    ->select('*')
                    ->from('bar'),
                'bar',
                false
            ]
        ];
    }

    /**
     * testWhere
     *
     * @param  string  $expt
     * @param  array   $wheres
     *
     * @return  void
     *
     * @dataProvider whereProvider
     */
    public function testWhere(string $expt, ...$wheres)
    {
        $this->instance->select('*')
            ->from('a');

        foreach ($wheres as $whereArgs) {
            $this->instance->where(...$whereArgs);
        }

        self::assertEquals(
            $expt,
            Escaper::replaceQueryParams(
                $this->instance,
                (string) $this->instance->render($bounded),
                $bounded
            )
        );
    }

    public function whereProvider(): array
    {
        return [
            'Simple where =' => [
                'SELECT * FROM "a" WHERE "foo" = \'bar\'',
                ['foo', 'bar']
            ],
            'Where <' => [
                'SELECT * FROM "a" WHERE "foo" < \'bar\'',
                ['foo', '<', 'bar']
            ],
            'Where chain' => [
                'SELECT * FROM "a" WHERE "foo" < 123 OR "baz" = \'bax\' AND "yoo" != \'goo\'',
                ['foo', '<', 123],
                ['baz', '=', 'bax', 'or'],
                ['yoo', '!=', 'goo', 'and'],
            ],
            'Where null' => [
                'SELECT * FROM "a" WHERE "foo" IS NULL',
                ['foo', null]
            ],
            'Where is null' => [
                'SELECT * FROM "a" WHERE "foo" IS NULL',
                ['foo', 'IS', null]
            ],
            'Where is not null' => [
                'SELECT * FROM "a" WHERE "foo" IS NOT NULL',
                ['foo', 'IS NOT', null]
            ],
            'Where = null' => [
                'SELECT * FROM "a" WHERE "foo" IS NULL',
                ['foo', '=', null]
            ],
            'Where != null' => [
                'SELECT * FROM "a" WHERE "foo" IS NOT NULL',
                ['foo', '!=', null]
            ],
            'Where in' => [
                'SELECT * FROM "a" WHERE "foo" IN (1, 2, \'yoo\')',
                ['foo', 'in', [1, 2, 'yoo']]
            ],
            'Where not exists sub query' => [
                'SELECT * FROM "a" WHERE "foo" NOT EXISTS (SELECT * FROM "flower" WHERE "id" = 5)',
                [
                    'foo',
                    'not exists',
                    self::createQuery()
                        ->select('*')
                        ->from('flower')
                        ->where('id', 5)
                ]
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
        $q = new Query(static function (string $value) {
            return addslashes($value);
        });

        $s = $q->quote("These are Simon's items");

        self::assertEquals("'These are Simon\'s items'", $s);

        $q = new Query(
            new class {
                public function escape(string $value): string
                {
                    return addslashes($value);
                }
            }
        );

        $s = $q->quote("These are Simon's items");

        self::assertEquals("'These are Simon\'s items'", $s);
    }

    /**
     * @see  Query::escape
     */
    public function testEscape(): void
    {
        $q = new Query(static function (string $value) {
            return addslashes($value);
        });

        $s = $q->escape("These are Simon's items");

        self::assertEquals("These are Simon\'s items", $s);

        $q = new Query(
            new class {
                public function escape(string $value): string
                {
                    return addslashes($value);
                }
            }
        );

        $s = $q->escape("These are Simon's items");

        self::assertEquals("These are Simon\'s items", $s);
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
        return new Query($conn ?: new MockEscaper());
    }

    protected function setUp(): void
    {
        $this->instance = self::createQuery();
    }

    protected function tearDown(): void
    {
    }
}
