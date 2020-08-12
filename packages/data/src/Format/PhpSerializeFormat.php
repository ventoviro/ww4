<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Data\Format;

/**
 * The PhpSerializeFormat class.
 */
class PhpSerializeFormat implements FormatInterface
{
    /**
     * dump
     *
     * @param  array|object  $data
     * @param  array         $options
     *
     * @return  string
     */
    public function dump($data, array $options = []): string
    {
        return serialize($data);
    }

    /**
     * parse
     *
     * @param  string  $string
     * @param  array   $options
     *
     * @return  array
     */
    public function parse(string $string, array $options = [])
    {
        return unserialize($string, $options);
    }
}
