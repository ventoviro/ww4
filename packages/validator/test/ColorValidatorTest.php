<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator\Test;

use Windwalker\Validator\Rule\ColorValidator;

/**
 * Test class of ColorValidator
 *
 * @since 2.0
 */
class ColorValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var ColorValidator
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
        $this->instance = new ColorValidator();
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
     * testValidate
     *
     * @return  void
     *
     * @cover  \Windwalker\Validator\Rule\AlnumValidator
     */
    public function testValidate()
    {
        $this->assertTrue($this->instance->test('#f2f2f2'));
        $this->assertTrue($this->instance->test('#F2F2F2'));
        $this->assertTrue($this->instance->test('#F2F'));
        $this->assertTrue($this->instance->test('#000'));

        $this->assertFalse($this->instance->test('#F2#$F2'));
        $this->assertFalse($this->instance->test('f2f2f2'));
        $this->assertFalse($this->instance->test('#12345678'));
        $this->assertFalse($this->instance->test('#UUUUUU'));
    }
}
