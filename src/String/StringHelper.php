<?php
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */
declare(strict_types = 1);

namespace Windwalker\String;

/**
 * The StringHelper class.
 *
 * @since  __DEPLOY_VERSION__
 */
class StringHelper
{

    public static function at(string $string, int $pos)
    {
        return Utf8String::substr($string, $pos, 1);
    }

    public static function between(
        string $string,
        string $start,
        string $end,
        int $offset = 0,
        string $encoding = null
    ) : string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        $startIndex = Utf8String::strpos($string, $start, $offset, $encoding);

        if ($startIndex === false) {
            return '';
        }

        $substrIndex = $startIndex + Utf8String::strlen($start, $encoding);

        $endIndex = Utf8String::strpos($string, $end, $substrIndex, $encoding);

        if ($endIndex === false) {
            return '';
        }

        return  Utf8String::substr($string, $substrIndex, $endIndex - $substrIndex);
    }

    public static function collapseWhitespaces(string $string) : string
    {
        $string = preg_replace('/\s\s+/', ' ', $string);

        return trim(preg_replace('/\s+/', ' ', $string));
    }

    /**
     * contains
     *
     * @param string $string
     * @param string $search
     * @param bool   $caseSensitive
     * @param string $encoding
     *
     * @return bool
     */
    public static function contains(
        string $string,
        string $search,
        bool $caseSensitive = true,
        string $encoding = null
    ): bool {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if ($caseSensitive) {
            return Utf8String::strpos($string, $search, 0, $encoding) !== false;
        } else {
            return Utf8String::stripos($string, $search, 0, $encoding) !== false;
        }
    }


}
