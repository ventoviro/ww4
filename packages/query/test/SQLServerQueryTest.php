<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Grammar\SQLServerGrammar;

use function Windwalker\raw;

/**
 * The SqlsrvQueryTest class.
 */
class SQLServerQueryTest extends QueryTest
{
    protected static array $nameQuote = ['[', ']'];

    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function createGrammar(): AbstractGrammar
    {
        return new SQLServerGrammar();
    }

    public function testInsert(): void
    {
        $this->instance->insert('foo', 'id')
            ->columns('id', 'title', ['foo', 'bar'], 'yoo')
            ->values(
                [1, 'A', 'a', null, raw('CURRENT_TIMESTAMP()')],
                [2, 'B', 'b', null, raw('CURRENT_TIMESTAMP()')],
                [3, 'C', 'c', null, raw('CURRENT_TIMESTAMP()')]
            );

        self::assertSqlEquals(
            <<<SQL
SET IDENTITY_INSERT "foo" ON;
INSERT INTO "foo"
("id", "title", "foo", "bar", "yoo")
VALUES
    (1, 'A', 'a', NULL, CURRENT_TIMESTAMP()),
    (2, 'B', 'b', NULL, CURRENT_TIMESTAMP()),
    (3, 'C', 'c', NULL, CURRENT_TIMESTAMP())
; SET IDENTITY_INSERT "foo" OFF;
SQL
            ,
            $this->instance
        );

        $q = self::createQuery()
            ->insert('foo')
            ->set('id', 1)
            ->set([
                      'title' => 'A',
                      'foo' => 'a',
                      'bar' => null,
                      'yoo' => raw('CURRENT_TIMESTAMP()')
                  ]);

        self::assertSqlEquals(
            <<<SQL
INSERT INTO "foo" SET "id" = 1, "title" = 'A', "foo" = 'a', "bar" = NULL, "yoo" = CURRENT_TIMESTAMP()
SQL
            ,
            $q
        );

        $q = self::createQuery()
            ->insert('foo')
            ->columns('id', 'title')
            ->values(
                self::createQuery()
                    ->select('id', 'title')
                    ->from('articles'),
                self::createQuery()
                    ->select('id', 'title')
                    ->from('categories')
            );

        self::assertSqlEquals(
            <<<SQL
INSERT INTO "foo" ("id", "title")
(SELECT "id", "title" FROM "articles") UNION (SELECT "id", "title" FROM "categories")
SQL
            ,
            $q
        );
    }

    public function testLimitOffset()
    {
        // Limit
        $q = self::createQuery()
            ->select('*')
            ->from('foo')
            ->order('id')
            ->limit(5);

        self::assertSqlEquals(
            'SELECT TOP 5 * FROM "foo" ORDER BY "id"',
            $q->render()
        );

        // Offset
        $q = self::createQuery()
            ->select('*')
            ->from('foo')
            ->order('id')
            ->offset(10);

        // Only offset will not work
        self::assertSqlEquals(
            'SELECT * FROM (SELECT *, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS RowNumber FROM ( SELECT * FROM [foo] ORDER BY [id] ) AS A) AS A WHERE RowNumber > 10',
            $q->render()
        );

        // Limit & Offset
        $q = self::createQuery()
            ->select('*')
            ->from('foo')
            ->order('id')
            ->limit(5)
            ->offset(15);

        self::assertSqlEquals(
            'SELECT * FROM (SELECT *, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS RowNumber FROM ( SELECT TOP 20 * FROM [foo] ORDER BY [id] ) AS A) AS A WHERE RowNumber > 15',
            $q->render()
        );
    }
}
