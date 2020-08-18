<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\DI\AttributesResolver;
use Windwalker\DI\Container;
use Windwalker\DI\Test\Attributes\Methods\ToUpper;

/**
 * The AttributeTest class.
 */
class AttributeTest extends TestCase
{
    protected ?Container $instance;

    public function testMethodAttributes()
    {
        $this->instance->getAttributesResolver()
            ->registerAttribute(ToUpper::class, AttributesResolver::METHODS);

        $obj = new class {
            @@ToUpper
            public function foo()
            {
                return 'foo';
            }
        };

        $result = $this->instance->call([$obj, 'foo'], [1, 2, 3]);

        show($result);
    }

    protected function setUp(): void
    {
        $this->instance = new Container();
    }

    protected function tearDown(): void
    {
    }
}
