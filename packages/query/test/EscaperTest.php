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
use Windwalker\Query\Bounded\BoundedHelper;
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
