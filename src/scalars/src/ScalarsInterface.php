<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

/**
 * Interface ScalarsInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface ScalarsInterface
{
    public function toNumber(): NumberObject;

    public function toString(): StringObject;

    public function toArray(): ArrayObject;
}
