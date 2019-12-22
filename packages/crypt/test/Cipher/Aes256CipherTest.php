<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Crypt\Test\Cipher;

use Windwalker\Crypt\Cipher2\Aes256Cipher;

/**
 * Test class of CipherBlowfish
 *
 * @since 3.0
 */
class Aes256CipherTest extends AbstractOpensslTestCase
{
    /**
     * Test instance.
     *
     * @var Aes256Cipher
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
        $this->instance = new Aes256Cipher();
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
     * Method to test getIVSize().
     *
     * @return void
     */
    public function testGetIVSize()
    {
        $this->assertEquals(16, $this->instance->getIVSize());
    }
}
