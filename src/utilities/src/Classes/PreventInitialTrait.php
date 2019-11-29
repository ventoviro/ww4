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
 * Trait PreventInitialTrait
 *
 * @since  __DEPLOY_VERSION__
 */
trait PreventInitialTrait
{
    /**
     * Prevent implement class.
     */
    protected function __construct()
    {
        //
    }
}
