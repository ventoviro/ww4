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
use Windwalker\Query\Clause;

/**
 * The ClauseTest class.
 */
class ClauseTest extends TestCase
{
    /**
     * @var Clause
     */
    protected $instance;

    /**
     * @param  string  $name
     * @param  array   $elements
     * @param  string  $glue
     * @param  string  $expected
     *
     * @see  Clause::__toString
     *
     * @dataProvider basicUsageProvider
     */
    public function testBasicUsage(string $name, array $elements, string $glue, string $expected): void
    {
        self::assertEquals(
            $expected,
            (string) new Clause($name, $elements, $glue)
        );
    }

    public function basicUsageProvider(): array
    {
        return [
            [
                'WHERE',
                ['foo > 0'],
                ' AND ',
                'WHERE foo > 0'
            ],
            [
                'WHERE',
                ['foo > 0', "bar = '123'"],
                ' AND ',
                'WHERE foo > 0 AND bar = \'123\''
            ],
            [
                'IN()',
                [1, 2, 3],
                ', ',
                'IN(1, 2, 3)'
            ],
            [
                '()',
                ['a = b', 'c = d'],
                ' OR ',
                '(a = b OR c = d)'
            ],
        ];
    }

    public function testNested(): void
    {
        $clause = new Clause('WHERE');

        $clause->append(new Clause('', ['foo', '=', "'bar'"]));
        $clause->append(new Clause('OR', ['foo', '<', 5]));
        $clause->append(new Clause('AND ()', [
            new Clause('', ['flower', '=', "'sakura'"]),
            new Clause('OR', ['flower', 'IS', 'NULL']),
        ]));

        self::assertEquals(
            'WHERE foo = \'bar\' OR foo < 5 AND (flower = \'sakura\' OR flower IS NULL)',
            (string) $clause
        );
    }

    /**
     * @see  Clause::setGlue
     */
    public function testSetGlue(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::setName
     */
    public function testSetName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::render
     */
    public function testRender(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::getGlue
     */
    public function testGetGlue(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::getElements
     */
    public function testGetElements(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::getName
     */
    public function testGetName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::append
     */
    public function testAppend(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Clause::__clone
     */
    public function test__clone(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
