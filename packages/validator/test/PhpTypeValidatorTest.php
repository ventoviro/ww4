<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 Asikart.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator\Rule\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Validator\Rule\PhpTypeValidator;

/**
 * Test class of \Windwalker\Validator\Rule\PhpTypeValidator
 *
 * @since 3.2
 */
class PhpTypeValidatorTest extends TestCase
{
    /**
     * Test instance.
     *
     * @var PhpTypeValidator
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = new PhpTypeValidator('ARRAY');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Method to test __construct().
     *
     * @return void
     *
     * @covers \Windwalker\Validator\Rule\PhpTypeValidator::__construct
     * @covers \Windwalker\Validator\Rule\PhpTypeValidator::getType
     */
    public function testConstruct()
    {
        self::assertEquals('array', $this->instance->getType());
    }

    /**
     * Method to test setType().
     *
     * @return void
     *
     * @covers \Windwalker\Validator\Rule\PhpTypeValidator::setType
     */
    public function testValidate()
    {
        self::assertTrue($this->instance->test([]));
        self::assertFalse($this->instance->test(''));

        self::assertTrue($this->instance->setType(\stdClass::class)->test(new \stdClass()));
        self::assertTrue($this->instance->setType('numeric')->test('1.2'));
        self::assertTrue($this->instance->setType('float')->test(1.2));
        self::assertTrue($this->instance->setType('double')->test(1.2));
        self::assertTrue($this->instance->setType('scalar')->test('abc'));
        self::assertFalse($this->instance->setType('scalar')->test(null));
        self::assertFalse($this->instance->setType('scalar')->test([]));
        self::assertTrue($this->instance->setType('callable')->test('trim'));
        self::assertTrue($this->instance->setType('array')->test([]));
        self::assertTrue($this->instance->setType('object')->test(new \stdClass()));
    }
}
