<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Crypt\Test;

use Windwalker\Crypt\CryptHelper;

/**
 * Test class of CryptHelper
 *
 * @since 3.1.3
 */
class CryptHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * limitIntegerProvider
     *
     * @return  array
     */
    public function limitIntegerProvider()
    {
        return [
            [10, 5, 10, 15],
            [15, 20, 10, 15],
            [10, 5, 10, null],
            [20, 20, 10, null],
            [5, 5, null, 15],
            [15, 20, null, 15],
        ];
    }

    /**
     * Method to test repeatToLength().
     *
     * @return void
     *
     * @covers \Windwalker\Crypt\CryptHelper::repeatToLength
     */
    public function testRepeatToLength()
    {
        $this->assertEquals('abcabc', CryptHelper::repeatToLength('abc', 5));
        $this->assertEquals('abc', CryptHelper::repeatToLength('abc', 2));
        $this->assertEquals('abcab', CryptHelper::repeatToLength('abc', 5, true));

        // No effect if length less than string
        $this->assertEquals('abc', CryptHelper::repeatToLength('abc', 1, true));
    }

    /**
     * Method to test getLength().
     *
     * @return void
     *
     * @covers \Windwalker\Crypt\CryptHelper::strlen
     */
    public function testGetLength()
    {
        $this->assertEquals(7, CryptHelper::strlen('Ang Lee'));
        $this->assertEquals(6, CryptHelper::strlen('李安'));
    }
}
