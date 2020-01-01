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
class Grammar
{
    protected $nameQuote = ['"', '"'];

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

    public function compileSelect(Query $query): string
    {
        $sql = (string) $query->getSelect();

        if ($form = $query->getFrom()) {
            $sql .= ' ' . $form;
        }

        return $sql;
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

    public function quoteName(string $name): string
    {
        if (stripos($name, ' as ') !== false) {
            [$name, $alias] = preg_split('/ as /i', $name);

            return $this->quoteName($name) . ' AS ' . $this->quoteName($alias);
        }

        if (strpos($name, '.') !== false) {
            [$name1, $name2] = explode('.', $name);

            return $this->quoteName($name1) . '.' . $this->quoteName($name2);
        }

        return $this->nameQuote[0] . $name . $this->nameQuote[1];
    }
}
