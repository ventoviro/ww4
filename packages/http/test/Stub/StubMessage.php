<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Http\Test\Stub;

use Psr\Http\Message\MessageInterface;
use Windwalker\Http\MessageTrait;

/**
 * The StubMessage class.
 *
 * @since  2.1
 */
class StubMessage implements MessageInterface
{
    use MessageTrait;
}
