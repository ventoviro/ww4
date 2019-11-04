<?php
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */
declare(strict_types = 1);

use Windwalker\String\StringObject;

if (!function_exists('str')) {
    /**
     * str
     *
     * @param string      $string
     * @param null|string $encoding
     *
     * @return  StringObject
     */
    function str(string $string = '', ?string $encoding = StringObject::ENCODING_UTF8): StringObject
    {
        return new StringObject($string, $encoding);
    }
}
