<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace PHPSTORM_META {

    registerArgumentsSet(
        'query_join_types',
        'INNER',
        'OUTER',
        'LEFT',
        'RIGHT'
    );

    expectedArguments(
        \Windwalker\Query\Query::join(),
        0,
        argumentsSet('query_join_types')
    );

    registerArgumentsSet(
        'query_union_types',
        '',
        'DISTINCT',
        'ALL'
    );

    expectedArguments(
        \Windwalker\Query\Query::union(),
        1,
        argumentsSet('query_union_types')
    );
}
