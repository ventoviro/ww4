<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator;

/**
 * The ValidatorInterface class.
 *
 * @since  2.0
 */
interface ValidatorInterface
{
    /**
     * Test this value.
     *
     * @param  mixed  $value
     *
     * @return  boolean
     */
    public function test($value): bool;

    /**
     * Just invoke this object to test value.
     *
     * @param  mixed  $value
     *
     * @return  bool
     */
    public function __invoke($value): bool;

    /**
     * Get error message.
     *
     * @return  string
     */
    public function getError();
}
