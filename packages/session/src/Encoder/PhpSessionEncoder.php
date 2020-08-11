<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Encoder;

/**
 * The PhpSessionEncoder class.
 */
class PhpSessionEncoder implements EncoderInterface
{
    /**
     * encode
     *
     * @param  array  $data
     *
     * @return  string
     */
    public function encode(array $data): string
    {
        return session_encode()
    }

    /**
     * decode
     *
     * @param  string  $encoded
     *
     * @return  array|null
     */
    public function decode(string $encoded): ?array
    {
    }
}
