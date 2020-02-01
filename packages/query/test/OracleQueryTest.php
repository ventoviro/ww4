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
use Windwalker\Query\Grammar\OracleGrammar;

/**
 * The OracleQueryTest class.
 */
class OracleQueryTest extends QueryTest
{
    public static function createGrammar(): AbstractGrammar
    {
        return new OracleGrammar();
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
            'SELECT windwalker2.* FROM ( SELECT windwalker1.*, ROWNUM AS windwalker_db_rownum FROM ( SELECT * FROM "foo" ORDER BY "id" ) windwalker1 ) windwalker2 WHERE windwalker2.windwalker_db_rownum BETWEEN 1 AND 5',
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
            'SELECT windwalker2.* FROM ( SELECT windwalker1.*, ROWNUM AS windwalker_db_rownum FROM ( SELECT * FROM "foo" ORDER BY "id" ) windwalker1 ) windwalker2 WHERE windwalker2.windwalker_db_rownum > 11',
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
            'SELECT windwalker2.* FROM ( SELECT windwalker1.*, ROWNUM AS windwalker_db_rownum FROM ( SELECT * FROM "foo" ORDER BY "id" ) windwalker1 ) windwalker2 WHERE windwalker2.windwalker_db_rownum BETWEEN 16 AND 20',
            $q->render()
        );
    }
}
