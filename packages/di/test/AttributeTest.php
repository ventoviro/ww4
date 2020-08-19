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
use Windwalker\DI\Test\Injection\Attrs\ParamLower;
use Windwalker\DI\Test\Injection\Attrs\ToUpper;
use Windwalker\DI\Test\Injection\Attrs\Wrapped;
use Windwalker\DI\Test\Injection\InnerStub;
use Windwalker\DI\Test\Injection\StubInject;
use Windwalker\DI\Test\Injection\StubService;
use Windwalker\DI\Test\Injection\WiredClass;
use Windwalker\Scalars\StringObject;

use function Windwalker\str;

/**
 * The AttributeTest class.
 */
class AttributeTest extends TestCase
{
    protected ?Container $instance;

    public function testObjectDecorate()
    {
        $this->instance->getAttributesResolver()
            ->registerAttribute(Wrapped::class, AttributesResolver::CLASSES);

        $result = $this->instance->newInstance(InnerStub::class);

        self::assertInstanceOf(Wrapped::class, $result);
        self::assertInstanceOf(InnerStub::class, $result->instance);
    }

    public function testObjectWrapCreator()
    {
        $this->instance->getAttributesResolver()
            ->registerAttribute(Wrapped::class, AttributesResolver::CLASSES);

        $result = $this->instance->newInstance(WiredClass::class);

        show($result);
    }

    public function testMethodAttributes()
    {
        $this->instance->set('stub', fn () => new StubService());

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

        self::assertEquals(
            'FOO',
            $result
        );
    }

    public function testMethodParamAttributes()
    {
        $this->instance->set('stub', fn () => new StubService());

        $this->instance->getAttributesResolver()
            ->registerAttribute(ParamLower::class, AttributesResolver::PARAMETERS);

        $obj = new class {
            public function foo(@@ParamLower StringObject $foo)
            {
                return (string) $foo;
            }
        };

        $result = $this->instance->call([$obj, 'foo'], [str('FOO')]);

        self::assertEquals(
            'foo',
            $result
        );
    }

    public function testCallClosure()
    {
        $closure = function (StubService $stub, array &$options = []): StubService {
            $options['foo'] = 'bar';
            return $stub;
        };

        $options = [];

        $stub = $this->instance->call($closure, ['options' => &$options]);

        self::assertEquals(
            ['foo' => 'bar'],
            $options
        );
    }

    protected function setUp(): void
    {
        $this->instance = new Container();
    }

    protected function tearDown(): void
    {
    }
}
