<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;
use Windwalker\Query\Test\Mock\MockEscaper;

/**
 * The EscaperTest class.
 */
class EscaperTest extends TestCase
{
    /**
     * @var Escaper
     */
    protected $instance;

    /**
     * @see  Escaper::replaceQueryParams
     */
    public function testReplaceQueryParams(): void
    {
        $sql = 'SELECT * FROM foo WHERE foo = :foo AND bar = ? AND yoo IN(?, ?, ?) AND flower = :flower';

        $query = new Query();
        $query->bind([
            'baz',
            1,
            2,
            3,
            ':foo' => 'FOO',
            ':flower' => 'Sakura'
        ]);

        $sql2 = Escaper::replaceQueryParams(
            new MockEscaper(),
            $sql,
            $query->getBounded()
        );

        self::assertEquals(
            "SELECT * FROM foo WHERE foo = 'FOO' AND bar = 'baz' AND yoo IN(1, 2, 3) AND flower = 'Sakura'",
            $sql2
        );
    }

    /**
     * @see  Escaper::tryQuote
     */
    public function testQuote(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Escaper::stripQuote
     */
    public function testStripQuote(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Escaper::tryEscape
     */
    public function testEscape(): void
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
