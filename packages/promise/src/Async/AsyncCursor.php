<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Async;

/**
 * The AsyncCursor class.
 */
class AsyncCursor
{
    /**
     * @var mixed
     */
    protected $cursor;

    /**
     * AsyncCursor constructor.
     *
     * @param  mixed  $cursor
     */
    public function __construct($cursor = null)
    {
        $this->cursor = $cursor;
    }

    /**
     * Method to get property Cursor
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function get()
    {
        return $this->cursor;
    }
}
