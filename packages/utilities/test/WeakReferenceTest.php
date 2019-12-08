<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;

/**
 * The WeakReferenceTest class.
 */
class WeakReferenceTest extends TestCase
{
    public function testWeakReference(): void
    {
        if (PHP_VERSION_ID < 70400) {
            self::markTestSkipped('WeakReference only works after PHP7.4');
        }

        $a = new \stdClass();

        $ref = \WeakReference::create($a);

        self::assertInstanceOf(\stdClass::class, $ref->get());

        $a = null;

        self::assertNull($ref->get());
    }
}
