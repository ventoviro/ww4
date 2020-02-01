<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Reseter;

use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Query;

/**
 * The AbstractReseter class.
 */
abstract class AbstractReseter
{
    protected static $platform = '';

    /**
     * create
     *
     * @param  string  $platform
     *
     * @return  static
     */
    public static function create(string $platform)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($platform) . 'Reseter';

        return new $class();
    }

    abstract public function createDatabase(\PDO $pdo, string $dbname): void;

    abstract public function clearAllTables(\PDO $pdo, string $dbname): void;

    public static function qn(string $value): string
    {
        return AbstractGrammar::create(static::$platform)::quoteName($value);
    }

    public function createQuery($escaper = null): Query
    {
        return new Query($escaper, AbstractGrammar::create(static::$platform));
    }
}
