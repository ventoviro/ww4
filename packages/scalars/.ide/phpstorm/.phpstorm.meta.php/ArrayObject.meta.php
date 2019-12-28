<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace PHPSTORM_META {

    use Windwalker\Scalars\ArrayObject;

    // ArrayObject
    override(
        ArrayObject::as(0),
        map(
            [
                '' => '@',
            ]
        )
    );
}
