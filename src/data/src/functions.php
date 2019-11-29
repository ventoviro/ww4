<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

namespace Windwalker;

use Windwalker\Data\Collection;

function collect($storage): Collection
{
    return new Collection($storage);
}
