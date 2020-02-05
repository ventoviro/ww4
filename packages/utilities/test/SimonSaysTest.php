<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Utilities\SimonSays;

/**
 * The SimonSaysTest class.
 */
class SimonSaysTest extends TestCase
{
    public function testCount()
    {
        $says = new SimonSays();

        $result = $says->count(400);

        self::assertEquals(800, $result);
    }
}
