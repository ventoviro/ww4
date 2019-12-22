<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Crypt\Test;

use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use PHPUnit\Framework\TestCase;

/**
 * The SodiumTest class.
 */
class SodiumTest extends TestCase
{
    // public function testHalite()
    // {
    //     // $keyPath = __DIR__ . '/encryption.key';
    //
    //     $encryptionKey = $encKey = KeyFactory::generateEncryptionKey();
    //
    //     $message = new HiddenString('This is a confidential message for your eyes only.');
    //     $ciphertext = Crypto::encrypt($message, $encryptionKey);
    //
    //     $decrypted = Crypto::decrypt($ciphertext, $encryptionKey);
    //
    //     var_dump($decrypted->getString(), $message->getString()); // bool(true)
    // }

    public function testTest()
    {
        self::assertEquals('', '');
    }
}
