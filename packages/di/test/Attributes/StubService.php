<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Test\Attributes;

/**
 * The StubService class.
 *
 * @since  3.4.4
 */
class StubService
{
    /**
     * Property counter.
     *
     * @var  int
     */
    public static $counter = 0;

    /**
     * StubService constructor.
     */
    public function __construct()
    {
        static::$counter++;
    }

    /**
     * run
     *
     * @return  string
     *
     * @since  3.4.4
     */
    public function run()
    {
        return 'OK';
    }

    /**
     * getCounter
     *
     * @return  int
     *
     * @since  3.4.4
     */
    public function getCounter()
    {
        return static::$counter;
    }
}
