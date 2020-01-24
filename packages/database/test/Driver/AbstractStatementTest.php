<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Driver\AbstractStatement;
use Windwalker\Query\Test\QueryTestTrait;

/**
 * The AbstractStatementTest class.
 */
class AbstractStatementTest extends TestCase
{
    use QueryTestTrait;

    /**
     *
     * @param  string  $sql
     * @param  string  $symbol
     * @param  array   $params
     * @param  string  $expct
     * @param  array   $expctParams
     *
     * @see  AbstractStatement::replaceStatement
     *
     * @dataProvider replaceStatementProvider
     */
    public function testReplaceStatement(
        string $sql,
        string $symbol,
        array $params,
        string $expct,
        array $expctParams
    ): void {
        [$sql2, $params2] = AbstractStatement::replaceStatement($sql, $symbol, $params);

        self::assertSqlEquals(
            $expct,
            $sql2
        );

        self::assertEquals(
            $expctParams,
            $params2
        );
    }

    public function replaceStatementProvider(): array
    {
        return [
            [
                'SELECT * FROM foo WHERE id IN(?, ?) AND title != :title AND alias != :alias AND created_by = ?',
                '?',
                [
                    ':title' => 'A',
                    0 => 5,
                    1 => 7,
                    ':alias' => 'B',
                    2 => 123
                ],
                'SELECT * FROM foo WHERE id IN(?, ?) AND title != ? AND alias != ? AND created_by = ?',
                [
                    5,
                    7,
                    'A',
                    'B',
                    123
                ]
            ],
            [
                'SELECT * FROM foo WHERE id IN(?, ?) AND title != :title AND alias != :alias AND created_by = ?',
                '$%d',
                [
                    ':title' => 'A',
                    0 => 5,
                    1 => 7,
                    ':alias' => 'B',
                    2 => 123
                ],
                'SELECT * FROM foo WHERE id IN($1, $2) AND title != $3 AND alias != $4 AND created_by = $5',
                [
                    5,
                    7,
                    'A',
                    'B',
                    123
                ]
            ],
            [
                'SELECT * FROM foo WHERE id IN(?, ?) AND title != :title AND alias != :alias AND created_by = ?',
                '$%d',
                [
                    0 => 5,
                    1 => 7,
                    ':alias' => 'B',
                    2 => 123
                ],
                'SELECT * FROM foo WHERE id IN($1, $2) AND title != :title AND alias != $3 AND created_by = $4',
                [
                    5,
                    7,
                    'B',
                    123
                ]
            ],
        ];
    }
}
