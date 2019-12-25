<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Crypt;

/**
 * The CryptHelper class.
 *
 * @since  2.0
 */
class CryptHelper
{
    /**
     * limitInteger
     *
     * @param  integer  $int
     * @param  integer  $min
     * @param  integer  $max
     *
     * @return  integer
     */
    public static function limitInteger($int, $min = null, $max = null)
    {
        $int = (int) $int;

        if ($min !== null && $int < $min) {
            $int = $min;
        }

        if ($max !== null && $int > $max) {
            $int = $max;
        }

        return (int) $int;
    }

    /**
     * repeatToLength
     *
     * @param  string   $string
     * @param  integer  $length
     * @param  bool     $cut
     *
     * @return  string
     */
    public static function repeatToLength(string $string, int $length, bool $cut = false): string
    {
        if (strlen($string) >= $length) {
            return $string;
        }

        $string = str_repeat($string, (int) ceil($length / strlen($string)));

        if ($cut) {
            $string = substr($string, 0, $length);
        }

        return $string;
    }

    /**
     * Generate random bytes.
     *
     * @param  integer  $length  Length of the random data to generate
     *
     * @note    This method is based on Joomla Crypt.
     * @return  string  Random binary data
     *
     * @throws \Exception
     * @since   2.0
     */
    public static function genRandomBytes(int $length = 16): string
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }

        $sslStr = '';

        /*
         * If a secure randomness generator exists use it.
         */
        if (function_exists('openssl_random_pseudo_bytes')) {
            $sslStr = openssl_random_pseudo_bytes($length, $strong);

            if ($strong !== false && $sslStr !== false) {
                return $sslStr;
            }
        }

        /*
         * Collect any entropy available in the system along with a number
         * of time measurements of operating system randomness.
         */
        $bitsPerRound  = 2;
        $maxTimeMicro  = 400;
        $shaHashLength = 20;
        $randomStr     = '';
        $total         = $length;

        // Check if we can use /dev/urandom.
        $urandom = false;
        $handle  = null;

        if (@is_readable('/dev/urandom')) {
            $handle = @fopen('/dev/urandom', 'rb');

            if ($handle) {
                $urandom = true;
            }
        }

        while ($length > strlen($randomStr)) {
            $bytes = ($total > $shaHashLength) ? $shaHashLength : $total;
            $total -= $bytes;
            /*
             * Collect any entropy available from the PHP system and filesystem.
             * If we have ssl data that isn't strong, we use it once.
             */
            $entropy = mt_rand() . uniqid(mt_rand(), true) . $sslStr;
            $entropy .= implode('', @fstat(fopen(__FILE__, 'r')));
            $entropy .= memory_get_usage();
            $sslStr  = '';

            if ($urandom) {
                stream_set_read_buffer($handle, 0);
                $entropy .= @fread($handle, $bytes);
            } else {
                /*
                 * There is no external source of entropy so we repeat calls
                 * to mt_rand until we are assured there's real randomness in
                 * the result.
                 *
                 * Measure the time that the operations will take on average.
                 */
                $samples  = 3;
                $duration = 0;

                for ($pass = 0; $pass < $samples; ++$pass) {
                    $microStart = microtime(true) * 1000000;
                    $hash       = sha1(mt_rand(), true);

                    for ($count = 0; $count < 50; ++$count) {
                        $hash = sha1($hash, true);
                    }

                    $microEnd = microtime(true) * 1000000;
                    $entropy  .= $microStart . $microEnd;

                    if ($microStart >= $microEnd) {
                        $microEnd += 1000000;
                    }

                    $duration += $microEnd - $microStart;
                }

                $duration = $duration / $samples;

                /*
                 * Based on the average time, determine the total rounds so that
                 * the total running time is bounded to a reasonable number.
                 */
                $rounds = (int) (($maxTimeMicro / $duration) * 50);

                /*
                 * Take additional measurements. On average we can expect
                 * at least $bitsPerRound bits of entropy from each measurement.
                 */
                $iter = $bytes * (int) ceil(8 / $bitsPerRound);

                for ($pass = 0; $pass < $iter; ++$pass) {
                    $microStart = microtime(true);
                    $hash       = sha1(mt_rand(), true);

                    for ($count = 0; $count < $rounds; ++$count) {
                        $hash = sha1($hash, true);
                    }

                    $entropy .= $microStart . microtime(true);
                }
            }

            $randomStr .= sha1($entropy, true);
        }

        if ($urandom) {
            @fclose($handle);
        }

        return substr($randomStr, 0, $length);
    }

    /**
     * Use a derivative of HKDF to derive multiple keys from one.
     * http://tools.ietf.org/html/rfc5869
     *
     * This is a variant from hash_hkdf() and instead uses BLAKE2b provided by
     * libsodium.
     *
     * Important: instead of a true HKDF (from HMAC) construct, this uses the
     * crypto_generichash() key parameter. This is *probably* okay.
     *
     * @note This method is port of Halite.
     *
     * @param  string  $ikm     Initial Keying Material
     * @param  int     $length  How many bytes?
     * @param  string  $info    What sort of key are we deriving?
     * @param  string  $salt
     *
     * @return string
     *
     * @throws \SodiumException
     */
    public static function hkdfBlake2b(
        string $ikm,
        int $length,
        string $info = '',
        string $salt = ''
    ): string {
        // Sanity-check the desired output length.
        if ($length < 0 || $length > (255 * \SODIUM_CRYPTO_GENERICHASH_KEYBYTES)) {
            throw new \InvalidArgumentException(
                'Argument 2: Bad HKDF Digest Length'
            );
        }

        // "If [salt] not provided, is set to a string of HashLen zeroes."
        if (empty($salt)) {
            $salt = \str_repeat("\x00", \SODIUM_CRYPTO_GENERICHASH_KEYBYTES);
        }

        // HKDF-Extract:
        // PRK = HMAC-Hash(salt, IKM)
        // The salt is the HMAC key.
        $prk = \sodium_crypto_generichash($ikm, $salt);

        // @note $prk should less than SODIUM_CRYPTO_GENERICHASH_KEYBYTES.

        // HKDF-Expand:
        // T(0) = ''
        $t          = '';
        $last_block = '';

        for ($block_index = 1; static::strlen($t) < $length; ++$block_index) {
            // T(i) = HMAC-Hash(PRK, T(i-1) | info | 0x??)
            $last_block = \sodium_crypto_generichash(
                $last_block . $info . \chr($block_index),
                $prk,
                \SODIUM_CRYPTO_GENERICHASH_BYTES
            );
            // T = T(1) | T(2) | T(3) | ... | T(N)
            $t .= $last_block;
        }

        // ORM = first L octets of T
        /** @var string $orm */
        $orm = static::substr($t, 0, $length);

        return $orm;
    }

    /**
     * mb safe string length calculator
     *
     * @param  string  $binaryString  The binary string return from crypt().
     *
     * @return  integer  String length.
     *
     * @since   2.0.4
     */
    public static function strlen(string $binaryString): int
    {
        return mb_strlen($binaryString, '8bit');
    }

    /**
     * substr
     *
     * @param  string  $str
     * @param  int     $start
     * @param  null    $length
     *
     * @return  string
     */
    public static function substr(
        string $str,
        int $start = 0,
        $length = null
    ): string {
        if ($length === 0) {
            return '';
        }

        return \mb_substr($str, $start, $length, '8bit');
    }

    /**
     * PHP 7 uses interned strings. We don't want altering this one to alter
     * the original string.
     *
     * @param  string  $string
     *
     * @return  string
     */
    public static function strcpy(string $string): string
    {
        $len = mb_strlen($string);

        $new   = '';
        $chunk = max($len >> 1, 1);

        for ($i = 0; $i < $len; $i += $chunk) {
            $new .= mb_substr($string, $i, $chunk);
        }

        return $new;
    }
}
