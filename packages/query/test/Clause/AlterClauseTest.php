<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test\Clause;

use Windwalker\Query\Clause\AlterClause;
use PHPUnit\Framework\TestCase;
use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Grammar\BaseGrammar;
use Windwalker\Query\Query;
use Windwalker\Query\Test\Mock\MockEscaper;
use Windwalker\Query\Test\QueryTestTrait;

class AlterClauseTest extends TestCase
{
    public function testRender(): void
    {
        $alter = self::createQuery()->alter('TABLE', 'foo');
        $alter->addIndex('idx_sakura', ['id', 'sakura']);

        self::assertEquals("ALTER TABLE \"foo\"\nADD INDEX \"idx_sakura\" (id,sakura)", (string) $alter);
    }

    public static function createQuery($conn = null): Query
    {
        return new Query($conn ?: new MockEscaper(), new BaseGrammar());
    }
}
