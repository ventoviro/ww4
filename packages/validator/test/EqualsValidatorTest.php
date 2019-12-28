<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator\Test;

use Windwalker\Validator\Rule\EqualsValidator;

/**
 * Test class of EqualsValidator
 *
 * @since 2.0
 */
class EqualsValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * getInstance
     *
     * @param  string  $compare
     * @param  bool    $strict
     *
     * @return  EqualsValidator
     */
    protected function getInstance($compare, $strict = false)
    {
        return new EqualsValidator($compare, $strict);
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
     * Method to test test().
     *
     * @return void
     *
     * @covers \Windwalker\Validator\Rule\EqualsValidator::doTest
     */
    public function testValidate()
    {
        $this->assertTrue($this->getInstance('abc')->test('abc'));

        $this->assertTrue($this->getInstance('1')->test(1));

        $this->assertTrue($this->getInstance(true)->test(1));

        $this->assertFalse($this->getInstance(true, true)->test(1));

        $this->assertFalse($this->getInstance(1, true)->test('1'));

        $this->assertFalse($this->getInstance(1.5, true)->test('1.5'));
    }
}
