<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Crypt\Test\Cipher;

use Windwalker\Crypt\Cipher\PhpAesCipher;
use Windwalker\Crypt\CryptHelper;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;

/**
 * Test class of PhpAesCipher
 *
 * @since 3.0
 */
class PhpAesCipherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var PhpAesCipher
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
        $this->instance = new PhpAesCipher();
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
     * Method to test decrypt().
     *
     * @return void
     * @throws \Exception
     */
    public function testDecrypt()
    {
        $data = 'windwalker';

        $key = new Key(random_bytes(16));

        $result = $this->instance->decrypt(
            $this->instance->encrypt(new HiddenString($data), $key),
            $key
        );

        $this->assertEquals($data, $result);
    }
}
