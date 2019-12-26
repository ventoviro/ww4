<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Crypt\Symmetric;

use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;
use Windwalker\Crypt\SafeEncoder;

/**
 * Interface CipherInterface
 *
 * @since  2.0
 */
interface CipherInterface
{
    /**
     * Decrypt string.
     *
     * @param  string  $str
     * @param  Key     $key
     * @param  string  $encoder
     *
     * @return  HiddenString
     */
    public function decrypt(string $str, Key $key, string $encoder = SafeEncoder::BASE64URLSAFE): HiddenString;

    /**
     * encrypt
     *
     * @param  HiddenString  $str
     * @param  Key           $key
     * @param  string        $encoder
     *
     * @return  string
     */
    public function encrypt(HiddenString $str, Key $key, string $encoder = SafeEncoder::BASE64URLSAFE): string;
}
