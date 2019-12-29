<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Classes;

/**
 * The FlowControlTrait class.
 */
trait FlowControlTrait
{
    /**
     * Pipe a callback and return the result.
     *
     * @param  callable  $callback
     * @param  array     $args
     *
     * @return  static
     */
    public function pipe(callable $callback, ...$args)
    {
        return $callback($this, ...$args);
    }

    /**
     * Tap a callback and return self.
     *
     * @param  callable  $callback
     * @param  mixed     ...$args
     *
     * @return  static
     */
    public function tap(callable $callback, ...$args)
    {
        $callback($this, ...$args);

        return $this;
    }

    /**
     * Pipe True/False callback based on a boolean value.
     *
     * @param  bool           $allow
     * @param  callable|null  $trueCallback
     * @param  callable|null  $falseCallback
     * @param  mixed          ...$args
     *
     * @return  static
     */
    public function pipeWhen(bool $allow, ?callable $trueCallback = null, ?callable $falseCallback = null, ...$args)
    {
        if ($allow) {
            return $trueCallback ? $trueCallback($this, ...$args) : $this;
        }

        return $falseCallback ? $falseCallback($this, ...$args) : $this;
    }

    /**
     * Tap True/False callback based on a boolean value.
     *
     * @param  bool           $allow
     * @param  callable|null  $trueCallback
     * @param  callable|null  $falseCallback
     * @param  mixed          ...$args
     *
     * @return  static
     */
    public function tapWhen(bool $allow, ?callable $trueCallback = null, ?callable $falseCallback = null, ...$args)
    {
        if ($allow) {
            $trueCallback ? $trueCallback($this, ...$args) : null;
        } else {
            $falseCallback ? $falseCallback($this, ...$args) : null;
        }

        return $this;
    }
}
