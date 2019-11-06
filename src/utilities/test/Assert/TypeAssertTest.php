<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Test\Assert;

use PHPUnit\Framework\TestCase;
use Windwalker\Utilities\Assert\TypeAssert;

/**
 * The TypeAssertTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TypeAssertTest extends TestCase
{
    /**
     * testThrowException
     *
     * @param  string  $class
     * @param  string  $message
     * @param          $value
     * @param  string  $caller
     * @param  string  $expected
     *
     * @return  void
     *
     * @dataProvider providerThrowException
     */
    public function testThrowException(string $class, string $message, $value, ?string $caller, string $expected): void
    {
        try {
            TypeAssert::throwException($class, $message, $value, $caller);
        } catch (\Throwable $e) {
            self::assertEquals($expected, $e->getMessage());
        }
    }

    public function providerThrowException(): array
    {
        return [
            'Auto get caller' => [
                \InvalidArgumentException::class,
                'Method %s must with type X, %s given.',
                5,
                null,
                'Method Windwalker\Utilities\Test\Assert\TypeAssertTest::testThrowException() must with type X, integer(5) given.'
            ],
            'Custom caller' => [
                \InvalidArgumentException::class,
                'Method %s must with type X, %s given.',
                5,
                'Foo::bar()',
                'Method Foo::bar() must with type X, integer(5) given.'
            ],
            'Custom arguments ordering' => [
                \InvalidArgumentException::class,
                'Got %2$s in %1$s',
                5,
                'Foo::bar()',
                'Got integer(5) in Foo::bar()'
            ],
            'No message arguments' => [
                \InvalidArgumentException::class,
                'Method wrong.',
                5,
                null,
                'Method wrong.'
            ],
        ];
    }
}
