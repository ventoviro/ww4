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
use Windwalker\Crypt\Symmetric\OpensslCipher;
use Windwalker\Crypt\Symmetric\SodiumCipher;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;
use Windwalker\Crypt\SafeEncoder;
use Windwalker\Data\Collection;
use function Windwalker\arr;

/**
 * Test class of OpensslCipher
 *
 * @since 2.0
 */
class OpensslCipherTest extends TestCase
{
    /**
     * Test instance.
     *
     * @var OpensslCipher
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
        //
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
     * @param  string  $method
     *
     * @return void
     *
     * @dataProvider methodsProvider
     */
    public function testEncrypt(string $method)
    {
        $key = new Key('hello');

        $cipher = new OpensslCipher($method);

        $data = $cipher->encrypt(new HiddenString('windwalker'), $key, SafeEncoder::HEX);

        $data = $cipher->decrypt($data, $key, SafeEncoder::HEX);

        $this->assertEquals('windwalker', $data->get());
    }

    public function methodsProvider(): array
    {
        return [
            ['AES-256-CBC'],
            ['AES-128-CFB'],
            ['BF-CBC'],
            ['BF-CFB'],
            ['IDEA-CBC'],
            ['AES128'],
            ['blowfish'],
        ];
    }

    /**
     * testLegacy
     *
     * @param  string  $method
     * @param  string  $str
     *
     * @return  void
     *
     * @dataProvider methodsLegacyProvider
     */
    public function testLegacy(string $method, string $str)
    {
        $key = new Key('foo');

        $cipher = new OpensslCipher($method, ['legacy' => true]);

        $data = $cipher->decrypt($str, $key);

        $this->assertEquals('windwalker', $data->get());
    }

    public function methodsLegacyProvider(): array
    {
        return [
            'AES-256-CBC' => [
                'AES-256-CBC',
                'EoklxV3fqZO5ma8XwWL7G2cK3i2k5AXKBz9m8PGeE2k=:LkfmL1i7Tjck+mxEnjgGKkb2VPIT8VC2pYV9Sr9BN24=:5CC6bDdRjeyNP+OAYuPolA==:i0i0TSq9oVZfxvcacicj7Q=='
            ],
            'des-ede3-cbc' => [
                'des-ede3-cbc',
                'vH8xcBwXQiXZ/YSvw+h0eWLbnftFHJNb5dc/Ob2vOHU=:MZIUaSKqBsnb0ZeMG5vJDzVwbyrrAPqYoqXNTO6RoUw=:/cEHJARlmjg=:fd0YQLROmEQRiEIyoOcXag=='
            ],
            'bf-cbc' => [
                'bf-cbc',
                '5ZTJ03ITnhshMxghJh/+b9d2+kSAPsGdHrcXXBp7Zso=:MS1jDSc5uxuf30ImrARNdXqn8oFexce+olpGj6PBbpA=:5WjBQfVXLuk=:S54cmXm3Lp3k42q7VRawVQ=='
            ],
        ];
    }
}
