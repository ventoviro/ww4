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
 * Interface EncoderInterface
 */
interface EncoderInterface
{
    public function encode(array $data): string;

    public function decode(string $encoded): ?array;
}
