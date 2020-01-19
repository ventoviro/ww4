<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Grammar\Grammar;

/**
 * The DefaultConnection class.
 */
class DefaultConnection
{
    protected static $escaper;

    /**
     * @var Grammar
     */
    protected static $grammar;

    /**
     * @return mixed
     */
    public static function getEscaper()
    {
        return static::$escaper;
    }

    /**
     * @param  mixed  $escaper
     *
     * @return  void
     */
    public static function setEscaper($escaper): void
    {
        static::$escaper = $escaper;
    }

    /**
     * @return Grammar
     */
    public static function getGrammar(): ?Grammar
    {
        return static::$grammar;
    }

    /**
     * @param  Grammar  $grammar
     *
     * @return  void
     */
    public static function setGrammar(Grammar $grammar)
    {
        static::$grammar = $grammar;
    }
}
