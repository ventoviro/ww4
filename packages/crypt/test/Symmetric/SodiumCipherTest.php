<?php

/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Crypt\Test\Symmetric;

use PHPUnit\Framework\TestCase;
use Windwalker\Crypt\Symmetric\SodiumCipher;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;

/**
 * Test class of Cipher3DES
 *
 * @since 2.0
 */
class SodiumCipherTest extends TestCase
{
    /**
     * Test instance.
     *
     * @var SodiumCipher
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
        $this->instance = new SodiumCipher();
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
     * Method to test encrypt().
     *
     * @return void
     *
     * @throws \SodiumException
     */
    public function testEncrypt()
    {
        $key = new Key('hello');

        $data = $this->instance->encrypt(new HiddenString('windwalker'), $key);

        $data = $this->instance->decrypt($data, $key);

        $this->assertEquals('windwalker', $data);
    }
}
