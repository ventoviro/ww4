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
    protected const HMAC_SIZE = SODIUM_CRYPTO_GENERICHASH_BYTES_MAX;

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
            $length - (static::HKDF_SALT_LEN + static::NONCE_SIZE + static::HMAC_SIZE)
        );

        $hmac = CryptHelper::substr(
            $message,
            $length - static::HMAC_SIZE
        );

        \sodium_memzero($message);

        /*
        Split our key into two keys: One for encryption, the other for
        authentication. By using separate keys, we can reasonably dismiss
        likely cross-protocol attacks.

        This uses salted HKDF to split the keys, which is why we need the
        salt in the first place.
        */
        [$encKey, $hmacKey] = static::derivateSecureKeys($key, $salt);

        if (!static::verifyHmac(
            $hmac,
            $salt . $nonce . $encrypted,
            $hmacKey
        )) {
            throw new \UnexpectedValueException('Invalid message authentication code');
        }

        \sodium_memzero($salt);
        \sodium_memzero($hmacKey);

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
        [$encKey, $hmacKey] = static::derivateSecureKeys($key, $salt);

        $encrypted = \sodium_crypto_stream_xor(
            $str->get(),
            $nonce,
            $encKey
        );

        \sodium_memzero($encKey);

        $hmac = \sodium_crypto_generichash(
            $salt . $nonce . $encrypted,
            $hmacKey,
            SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
        );

        \sodium_memzero($hmacKey);

        $message = $salt . $nonce . $encrypted . $hmac;

        // Wipe every superfluous piece of data from memory
        \sodium_memzero($nonce);
        \sodium_memzero($salt);
        \sodium_memzero($encrypted);
        \sodium_memzero($hmac);

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
    public static function derivateSecureKeys(
        Key $key,
        string $salt
    ): array {
        $binary = $key->get();

        return [
            CryptHelper::hkdfBlake2b(
                $binary,
                \SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
                'Windwalker|EncryptionKey',
                $salt
            ),
            CryptHelper::hkdfBlake2b(
                $binary,
                \SODIUM_CRYPTO_AUTH_KEYBYTES,
                'AuthenticationKeyFor_|Windwalker',
                $salt
            )
        ];
    }

    /**
     * verifyAuth
     *
     * @param  string  $auth
     * @param  string  $message
     * @param  string  $hmacKey
     *
     * @return  bool
     *
     * @throws \SodiumException
     */
    protected static function verifyHmac(
        string $auth,
        string $message,
        string $hmacKey
    ): bool {
        if (CryptHelper::strlen($auth) !== static::HMAC_SIZE) {
            throw new \InvalidArgumentException(
                'Argument 1: Message Authentication Code is not the correct length; is it encoded?'
            );
        }

        $calc = \sodium_crypto_generichash(
            $message,
            $hmacKey,
            static::HMAC_SIZE
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
