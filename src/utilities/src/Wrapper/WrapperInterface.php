<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Wrapper;

/**
 * Interface WrapperInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface WrapperInterface
{
    /**
     * Get wrapped value.
     *
     * @param mixed $src
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __invoke($src);
}
