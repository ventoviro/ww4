<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Clause\Clause;
use Windwalker\Query\DefaultConnection;
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;
use Windwalker\Utilities\Assert\ArgumentsAssert;

/**
 * The AbstractGrammar class.
 */
abstract class AbstractGrammar
{
    /**
     * @var string
     */
    protected static $name = '';

    /**
     * @var array
     */
    protected static $nameQuote = ['"', '"'];

    /**
     * @var string
     */
    protected static $nullDate = '0000-00-00 00:00:00';

    /**
     * @var string
     */
    protected static $dateFormat = 'Y-m-d H:i:s';

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * create
     *
     * @param  string|null  $name
     *
     * @return  static
     */
    public static function create(?string $name = null)
    {
        if ($name === null) {
            if ($grammar = DefaultConnection::getGrammar()) {
                return $grammar;
            }
        }

        $class = sprintf('%s\%sGrammar', __NAMESPACE__, ucfirst((string) $name));

        if (!class_exists($class)) {
            $class = BaseGrammar::class;
        }

        return new $class();
    }

    /**
     * Method to get property Name
     *
     * @return  string
     */
    public static function getName(): string
    {
        return static::$name;
    }

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
        if ($type === '') {
            throw new \InvalidArgumentException('Type shouldn\'t be empty string');
        }

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

        $sql = $this->$method($query);

        if ($suffix = $query->getSuffix()) {
            $sql .= ' ' . $suffix;
        }

        return $sql;
    }

    public function compileSelect(Query $query): string
    {
        $sql['select'] = (string) $query->getSelect();

        if ($form = $query->getFrom()) {
            $sql['from'] = $form;
        }

        if ($join = $query->getJoin()) {
            $sql['join'] = $join;
        }

        if ($where = $query->getWhere()) {
            $sql['where'] = $where;
        }

        if ($having = $query->getHaving()) {
            $sql['having'] = $having;
        }

        if ($group = $query->getGroup()) {
            $sql['group'] = $group;
        }

        if ($union = $query->getUnion()) {
            // Is full union
            if (!$query->getSelect()) {
                $union->setName('()');

                unset($sql['group']);
            }

            $sql['union'] = (string) $union;
        }

        // Only order and limit can after union
        if ($order = $query->getOrder()) {
            $sql['order'] = $order;
        }

        $sql = $this->compileLimit($query, $sql);

        return implode(' ', $sql);
    }

    public function compileInsert(Query $query): string
    {
        $sql['insert'] = $query->getInsert();

        if ($set = $query->getSet()) {
            $sql['set'] = $set;
        } else {
            if ($columns = $query->getColumns()) {
                $sql['columns'] = $columns;
            }

            if ($values = $query->getValues()) {
                $sql['values'] = $values;
            }
        }

        return trim(implode(' ', $sql));
    }

    public function compileUpdate(Query $query): string
    {
        $sql['update'] = $query->getUpdate();

        if ($join = $query->getJoin()) {
            $sql['join'] = $join;
        }

        if ($set = $query->getSet()) {
            $sql['set'] = $set;
        }

        if ($where = $query->getWhere()) {
            $sql['where'] = $where;
        }

        return trim(implode(' ', $sql));
    }

    public function compileDelete(Query $query): string
    {
        $sql['delete'] = $query->getDelete();

        if ($form = $query->getFrom()) {
            $sql['from'] = $form;
        }

        if ($join = $query->getJoin()) {
            $sql['join'] = $join;
        }

        if ($where = $query->getWhere()) {
            $sql['where'] = $where;
        }

        return trim(implode(' ', $sql));
    }

    public function compileCustom(Query $query): string
    {
        return (string) $query->getSql();
    }

    public static function quoteName(string $name): string
    {
        if ($name === '*') {
            return $name;
        }

        if (stripos($name, ' as ') !== false) {
            [$name, $alias] = preg_split('/ as /i', $name);

            return static::quoteName($name) . ' AS ' . static::quoteName($alias);
        }

        if (strpos($name, '.') !== false) {
            return implode(
                '.',
                array_map(
                    [static::class, 'quoteName'],
                    explode('.', $name)
                )
            );
        }

        return static::$nameQuote[0] . $name . static::$nameQuote[1];
    }

    /**
     * compileLimit
     *
     * @param  Query  $query
     * @param  array  $sql
     *
     * @return  array
     */
    public function compileLimit(Query $query, array $sql): array
    {
        $limit  = $query->getLimit();
        $offset = $query->getOffset();

        if ($limit !== null) {
            $limitOffset = new Clause('LIMIT', (int) $limit, ', ');

            if ($offset !== null) {
                $limitOffset->prepend($offset);
            }

            $sql['limit'] = $limitOffset;
        }

        return $sql;
    }

    /**
     * If no connection set, we escape it with default function.
     *
     * @param string $text
     *
     * @return  string
     */
    public function localEscape(string $text): string
    {
        $text = str_replace("'", "''", $text);

        return addcslashes($text, "\000\n\r\\\032");
    }

    public static function nullDate(): string
    {
        return static::$nullDate;
    }

    public static function dateFormat(): string
    {
        return static::$dateFormat;
    }

    /**
     * clause
     *
     * @param  string  $name
     * @param  mixed   $elements
     * @param  string  $glue
     *
     * @return  Clause
     */
    public static function clause(string $name = '', $elements = [], string $glue = ' '): Clause
    {
        return new Clause($name, $elements, $glue);
    }

    /**
     * build
     *
     * @param  string|null  ...$args
     *
     * @return  string
     */
    public static function build(?string ...$args): string
    {
        $sql = [];

        foreach ($args as $arg) {
            if ($arg === '' || $arg === null || $arg === false) {
                continue;
            }

            $sql[] = $arg;
        }

        return implode(' ', $sql);
    }

    /**
     * @param  Escaper  $escaper
     *
     * @return  static  Return self to support chaining.
     */
    public function setEscaper(Escaper $escaper)
    {
        $this->escaper = $escaper;

        return $this;
    }

    public function createQuery(): Query
    {
        return new Query($this->escaper, $this);
    }

    /**
     * dropTable
     *
     * @param  string  $table
     * @param  bool    $ifExists
     * @param  mixed   ...$options
     *
     * @return  string
     */
    public function dropTable(string $table, bool $ifExists = false, ...$options): string
    {
        return static::build(
            'DROP TABLE',
            $ifExists ? 'IF EXISTS' : null,
            self::quoteName($table),
            ...$options
        );
    }

    /**
     * dropTable
     *
     * @param  string  $table
     * @param  bool    $ifExists
     * @param  mixed   ...$options
     *
     * @return  string
     */
    public function dropView(string $table, bool $ifExists = false, ...$options): string
    {
        return static::build(
            'DROP VIEW',
            $ifExists ? 'IF EXISTS' : null,
            self::quoteName($table),
            ...$options
        );
    }
}
