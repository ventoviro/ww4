<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Crypt\Cipher;

use Windwalker\Crypt\CryptHelper;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;
use Windwalker\Crypt\SafeEncoder;

/**
 * A cipher to encrypt/decrypt data by libsodium.
 *
 * This class is a simpler modified version of Halite Crypto.
 * If you need more security features, please @see https://github.com/paragonie/halite
 */
class SodiumCipher implements CipherInterface
{
    protected const HKDF_SALT_LEN = 32;
    protected const NONCE_SIZE = SODIUM_CRYPTO_STREAM_NONCEBYTES;
    protected const AUTH_SIZE = SODIUM_CRYPTO_GENERICHASH_BYTES_MAX;

    /**
     * @inheritDoc
     * @throws \SodiumException
     */
    public function decrypt(string $str, Key $key, string $encoder = SafeEncoder::BASE64URLSAFE): HiddenString
    {
        $message = SafeEncoder::decode($encoder, $str);

        $length = CryptHelper::strlen($message);

        // Split string
        $salt      = CryptHelper::substr($message, 0, static::HKDF_SALT_LEN);
        $nonce     = CryptHelper::substr($message, static::HKDF_SALT_LEN, static::NONCE_SIZE);
        $encrypted = CryptHelper::substr(
            $message,
            static::HKDF_SALT_LEN + static::NONCE_SIZE,
            $length - (static::HKDF_SALT_LEN + static::NONCE_SIZE + static::AUTH_SIZE)
        );
        $auth      = CryptHelper::substr(
            $message,
            $length - static::AUTH_SIZE
        );

        \sodium_memzero($message);

        /*
        Split our key into two keys: One for encryption, the other for
        authentication. By using separate keys, we can reasonably dismiss
        likely cross-protocol attacks.

        This uses salted HKDF to split the keys, which is why we need the
        salt in the first place.
        */
        [$encKey, $authKey] = static::splitKeys($key, $salt);

        if (!static::verifyAuth(
            $auth,
            $salt . $nonce . $encrypted,
            $authKey
        )) {
            throw new \UnexpectedValueException('Invalid message authentication code');
        }

        \sodium_memzero($salt);
        \sodium_memzero($authKey);

        $plaintext = \sodium_crypto_stream_xor(
            $encrypted,
            $nonce,
            $encKey
        );

        \sodium_memzero($encrypted);
        \sodium_memzero($nonce);
        \sodium_memzero($encKey);

        return new HiddenString($plaintext);
    }

    /**
     * @inheritDoc
     * @throws \SodiumException
     * @throws \Exception
     */
    public function encrypt(HiddenString $str, Key $key, string $encoder = SafeEncoder::BASE64URLSAFE): string
    {
        $nonce = \random_bytes(\SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $salt = \random_bytes(static::HKDF_SALT_LEN);

        /*
        Split our key into two keys: One for encryption, the other for
        authentication. By using separate keys, we can reasonably dismiss
        likely cross-protocol attacks.

        This uses salted HKDF to split the keys, which is why we need the
        salt in the first place.
        */
        [$encKey, $authKey] = static::splitKeys($key, $salt);

        $encrypted = \sodium_crypto_stream_xor(
            $str->get(),
            $nonce,
            $encKey
        );

        \sodium_memzero($encKey);

        $auth = \sodium_crypto_generichash(
            $salt . $nonce . $encrypted,
            $authKey,
            SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
        );

        \sodium_memzero($authKey);

        $message = $salt . $nonce . $encrypted . $auth;

        // Wipe every superfluous piece of data from memory
        \sodium_memzero($nonce);
        \sodium_memzero($salt);
        \sodium_memzero($encrypted);
        \sodium_memzero($auth);

        return SafeEncoder::encode($encoder, $message);
    }

    /**
     * Split a key (using HKDF-BLAKE2b instead of HKDF-HMAC-*)
     *
     * @param  Key     $key
     * @param  string  $salt
     *
     * @return  array
     *
     * @throws \SodiumException
     */
    public static function splitKeys(
        Key $key,
        string $salt
    ): array {
        $binary = $key->get();

        return [
            CryptHelper::hkdfBlake2b(
                $binary,
                \SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
                'Halite|EncryptionKey',
                $salt
            ),
            CryptHelper::hkdfBlake2b(
                $binary,
                \SODIUM_CRYPTO_AUTH_KEYBYTES,
                'AuthenticationKeyFor_|Halite',
                $salt
            )
        ];
    }

    /**
     * verifyAuth
     *
     * @param  string  $auth
     * @param  string  $message
     * @param  string  $authKey
     *
     * @return  bool
     *
     * @throws \SodiumException
     */
    protected static function verifyAuth(
        string $auth,
        string $message,
        string $authKey
    ): bool {
        if (CryptHelper::strlen($auth) !== static::AUTH_SIZE) {
            throw new \InvalidArgumentException(
                'Argument 1: Message Authentication Code is not the correct length; is it encoded?'
            );
        }

        $calc = \sodium_crypto_generichash(
            $message,
            $authKey,
            static::AUTH_SIZE
        );

        $res = \hash_equals($auth, $calc);
        \sodium_memzero($calc);

        return $res;
    }

    /**
     * generateKey
     *
     * @return  Key
     *
     * @throws \Exception
     */
    public static function generateKey(): Key
    {
        return new Key(\random_bytes(\SODIUM_CRYPTO_STREAM_KEYBYTES));
    }
}
