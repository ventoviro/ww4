<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */
declare(strict_types=1);

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Test\Traits\BaseAssertionTrait;
use Windwalker\Utilities\TypeCast;

/**
 * The ArrayHelperTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TypeCastTest extends TestCase
{
    use BaseAssertionTrait;

    /**
     * testToArray
     *
     * @param $input
     * @param $recursive
     * @param $expect
     *
     * @return  void
     *
     * @dataProvider  providerTestToArray
     */
    public function testToArray($input, $recursive, $expect)
    {
        $this->assertEquals($expect, TypeCast::toArray($input, $recursive));
    }

    /**
     * Data provider for object inputs
     *
     * @return  array
     *
     * @since   2.0
     */
    public function providerTestToArray()
    {
        return [
            'string' => [
                'foo',
                false,
                ['foo']
            ],
            'array' => [
                ['foo'],
                false,
                ['foo']
            ],
            'array_recursive' => [
                [
                    'foo' => [
                        (object) ['bar' => 'bar'],
                        (object) ['baz' => 'baz']
                    ]
                ],
                true,
                [
                    'foo' => [
                        ['bar' => 'bar'],
                        ['baz' => 'baz']
                    ]
                ]
            ],
            'iterator' => [
                ['foo' => new \ArrayIterator(['bar' => 'baz'])],
                true,
                ['foo' => ['bar' => 'baz']]
            ]
        ];
    }

    /**
     * testToObject
     *
     * @param  mixed   $input
     * @param  mixed   $expect
     * @param  bool    $recursive
     * @param  string  $message
     *
     * @return  void
     *
     * @dataProvider providerTestToObject
     */
    public function testToObject($input, $expect, bool $recursive, string $message)
    {
        self::assertEquals($expect, TypeCast::toObject($input, $recursive), $message);
    }

    /**
     * providerTestToObject
     *
     * @return  array
     */
    public function providerTestToObject()
    {
        return [
            'single object' => [
                [
                    'integer' => 12,
                    'float' => 1.29999,
                    'string' => 'A Test String'
                ],
                (object) [
                    'integer' => 12,
                    'float' => 1.29999,
                    'string' => 'A Test String'
                ],
                false,
                'Should turn array into single object'
            ],
            'multiple objects' => [
                [
                    'first' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'second' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'third' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                ],
                (object) [
                    'first' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'second' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'third' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                ],
                true,
                'Should turn multiple dimension array into nested objects'
            ],
            'single object with class' => [
                [
                    'integer' => 12,
                    'float' => 1.29999,
                    'string' => 'A Test String'
                ],
                (object) [
                    'integer' => 12,
                    'float' => 1.29999,
                    'string' => 'A Test String'
                ],
                false,
                'Should turn array into single object'
            ],
            'multiple objects with class' => [
                [
                    'first' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'second' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'third' => [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                ],
                (object) [
                    'first' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'second' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                    'third' => (object) [
                        'integer' => 12,
                        'float' => 1.29999,
                        'string' => 'A Test String'
                    ],
                ],
                true,
                'Should turn multiple dimension array into nested objects'
            ],
        ];
    }
}
