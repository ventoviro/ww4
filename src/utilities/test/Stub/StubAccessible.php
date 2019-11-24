<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Test\Stub;

use Windwalker\Utilities\AccessibleTrait;
use Windwalker\Utilities\Contract\AccessibleInterface;

/**
 * The StubAccessible class.
 *
 * @since  __DEPLOY_VERSION__
 */
class StubAccessible implements AccessibleInterface
{
    use AccessibleTrait;

    /**
     * StubAccessible constructor.
     *
     * @param  array  $storage
     */
    public function __construct(array $storage = [])
    {
        $this->storage = $storage;
    }
}
