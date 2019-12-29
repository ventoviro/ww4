<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Query;

/**
 * The AbstractGrammar class.
 */
class AbstractGrammar
{
    /**
     * Compile Query object to SQL string.
     *
     * @param  string  $type
     * @param  Query   $query
     *
     * @return  string
     */
    public function compile(string $type, Query $query): string
    {
        $method = 'compile' . ucfirst($type);

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(
                sprintf(
                    '%s dose not support "%s" compiled',
                    static::class,
                    $type
                )
            );
        }

        return $this->$method($query);
    }

    public function compileSelect(Query $query)
    {
        //
    }

    public function compileInsert(Query $query)
    {
        //
    }

    public function compileUpdate(Query $query)
    {
        //
    }

    public function compileDelete(Query $query)
    {
        //
    }

    public function compileUnion(Query $query)
    {
        //
    }

    public function compileCustom(Query $query)
    {
        //
    }
}
