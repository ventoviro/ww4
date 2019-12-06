<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker;

/**
 * go
 *
 * @param  callable    $handler
 * @param  array|null  $params
 *
 * @return  mixed
 *
 * @since  __DEPLOY_VERSION__
 */
function go(callable $handler, $params = null)
{
    if (function_exists('\go')) {
        return \go($handler, $params);
    }

    return $handler();
}
