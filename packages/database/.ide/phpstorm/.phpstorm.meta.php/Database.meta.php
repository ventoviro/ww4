<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace PHPSTORM_META {

    override(
        \Windwalker\Database\Manager\WriterManager::insertOne(1),
        type(0)
    );

    override(
        \Windwalker\Database\Manager\WriterManager::updateOne(1),
        type(0)
    );

    override(
        \Windwalker\Database\Manager\WriterManager::saveOne(1),
        type(0)
    );

    override(
        \Windwalker\Database\Manager\WriterManager::insertMultiple(1),
        type(0)
    );

    override(
        \Windwalker\Database\Manager\WriterManager::updateMultiple(1),
        type(0)
    );

    override(
        \Windwalker\Database\Manager\WriterManager::saveMultiple(1),
        type(0)
    );
}
