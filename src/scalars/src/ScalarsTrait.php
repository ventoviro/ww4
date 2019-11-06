<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

/**
 * The ScalarsFactory class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ScalarsTrait
{
    /**
     * fromNative
     *
     * @param mixed $value
     *
     * @return  ArrayObject|StringObject|mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function fromNative($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_string($value)) {
            return new StringObject($value);
        }

        if (is_array($value)) {
            return new ArrayObject($value);
        }

        return $value;
    }
}
