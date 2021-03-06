<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker;

use Windwalker\Data\Collection;

function collect($storage = []): Collection
{
    return Collection::wrap($storage);
}
