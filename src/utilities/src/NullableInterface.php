<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities;

/**
 * Interface NullableInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface NullableInterface
{
    /**
     * isNull
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function isNull(): bool;

    /**
     * notNull
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function notNull(): bool;
}
