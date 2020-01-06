<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Crypt\Symmetric;

use Windwalker\Crypt\CryptHelper;
use Windwalker\Crypt\Exception\CryptException;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;
use Windwalker\Crypt\SafeEncoder;

/**
 * The Openssl Cipher class.
 *
 * @note   This cipher class referenced Eugene Fidelin and Peter Mortensen's code to prevent
 *        Chosen-Cipher and Timing attack.
 *
 * @see    http://stackoverflow.com/a/19445173
 *
 * @since  2.0
 */
class OpensslCipher implements CipherInterface
{
    public const PBKDF2_HASH_ALGORITHM = 'SHA256';

    public const PBKDF2_SALT_BYTE_SIZE = 32;

    public const PBKDF2_HASH_BYTE_SIZE = 32;

    /**
     * Property type.
     *
     * @var string
     */
    protected $method;

    /**
     * Property options.
     *
     * @var  array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param  string  $method
     * @param  array   $options
     *
     * @since  2.0
     */
    public function __construct(string $method, array $options = [])
    {
        if (!is_callable('openssl_encrypt')) {
            throw new CryptException('The openssl extension is not available.');
        }

        $this->options = array_merge(
            [
                'pbkdf2_iteration' => 12000,
                'legacy' => false,
            ],
            $options
        );

        $this->method = $method;
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $str, Key $key, string $encoder = SafeEncoder::BASE64): HiddenString
    {
        [$hmac, $salt, $iv, $encrypted] = explode(':', $str);

        $hmac      = SafeEncoder::decode($encoder, $hmac);
        $salt      = SafeEncoder::decode($encoder, $salt);
        $iv        = SafeEncoder::decode($encoder, $iv);
        $encrypted = SafeEncoder::decode($encoder, $encrypted);

        [$encKey, $hmacKey] = $this->derivateSecureKeys($key->get(), $salt);

        $calculatedHmac = $this->hmac($salt . $iv . $encrypted, $hmacKey);

        if (!hash_equals($calculatedHmac, $hmac)) {
            throw new CryptException('HMAC ERROR: Invalid HMAC.');
        }

        if ($this->options['legacy']) {
            $encKey = CryptHelper::repeatToLength($key->get(), 24, true);
        }

        $decrypted = openssl_decrypt($encrypted, $this->getMethod(), $encKey, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            throw new CryptException('Openssl decrypt failed: ' . openssl_error_string());
        }

        // Decrypt the data.
        return new HiddenString(trim($decrypted));
    }

    /**
     * @inheritDoc
     */
    public function encrypt(HiddenString $str, Key $key, string $encoder = SafeEncoder::BASE64): string
    {
        $salt = $this->randomPseudoBytes(static::PBKDF2_SALT_BYTE_SIZE);

        [$encKey, $hmacKey] = $this->derivateSecureKeys($key->get(), $salt);

        $iv = $this->getIV();

        if ($this->options['legacy']) {
            $encKey = CryptHelper::repeatToLength($key->get(), 24, true);
        }

        // Encrypt the data.
        $encrypted = openssl_encrypt(
            $str->get(),
            $this->getMethod(),
            $encKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        $hmac = $this->hmac($salt . $iv . $encrypted, $hmacKey);

        return implode(
            ':',
            [
                SafeEncoder::encode($encoder, $hmac),
                SafeEncoder::encode($encoder, $salt),
                SafeEncoder::encode($encoder, $iv),
                SafeEncoder::encode($encoder, $encrypted),
            ]
        );
    }

    /**
     * getIVSize
     *
     * @return  integer
     */
    public function getIVSize(): int
    {
        return openssl_cipher_iv_length($this->getMethod());
    }

    /**
     * randomPseudoBytes
     *
     * @param  int  $size
     *
     * @return  string
     *
     * @throws CryptException
     */
    protected function randomPseudoBytes(?int $size = null): string
    {
        $size = $size ?: static::PBKDF2_SALT_BYTE_SIZE;

        $bytes = openssl_random_pseudo_bytes($size, $isSourceStrong);

        if (false === $isSourceStrong || false === $bytes) {
            throw new CryptException('IV generation failed');
        }

        return $bytes;
    }

    /**
     * getIVKey
     *
     * @return  string
     *
     * @throws CryptException
     */
    public function getIV(): string
    {
        return $this->randomPseudoBytes($this->getIVSize());
    }

    /**
     * Creates secure PBKDF2 derivatives out of the password.
     *
     * @param  string  $key
     * @param  string  $salt
     *
     * @return array [$secureEncryptionKey, $secureHMACKey]
     * @throws CryptException
     */
    protected function derivateSecureKeys(string $key, string $salt): array
    {
        $iteration = $this->options['pbkdf2_iteration'] ?? 12000;

        return str_split(
            $this->pbkdf2(
                static::PBKDF2_HASH_ALGORITHM,
                $key,
                $salt,
                $iteration,
                static::PBKDF2_HASH_BYTE_SIZE * 2,
                true
            ),
            self::PBKDF2_HASH_BYTE_SIZE
        );
    }

    /**
     * Calculates HMAC for the message.
     *
     * @param  string  $message
     * @param  string  $hmacKey
     *
     * @return string
     */
    protected function hmac(string $message, string $hmacKey): string
    {
        return hash_hmac(self::PBKDF2_HASH_ALGORITHM, $message, $hmacKey, true);
    }

    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     *
     * @param  string  $algorithm  The hash algorithm to use. Recommended: SHA256
     * @param  string  $password   The password
     * @param  string  $salt       A salt that is unique to the password
     * @param  int     $count      Iteration count. Higher is better, but slower. Recommended: At least 1000
     * @param  int     $keyLength  The length of the derived key in bytes
     * @param  bool    $rawOutput  If true, the key is returned in raw binary format. Hex encoded otherwise
     *
     * @return string A $keyLength-byte key derived from the password and salt
     */
    protected function pbkdf2(
        string $algorithm,
        string $password,
        string $salt,
        int $count,
        int $keyLength,
        bool $rawOutput = false
    ): string {
        $algorithm = strtolower($algorithm);

        if (!in_array($algorithm, hash_algos(), true)) {
            throw new CryptException('PBKDF2 ERROR: Invalid hash algorithm.');
        }

        if ($count <= 0 || $keyLength <= 0) {
            throw new CryptException('PBKDF2 ERROR: Invalid parameters.');
        }

        return hash_pbkdf2($algorithm, $password, $salt, $count, $keyLength, $rawOutput);
    }

    /**
     * Method to get property Type
     *
     * @return  string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
