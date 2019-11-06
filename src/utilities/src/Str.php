<?php declare(strict_types = 1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */

namespace Windwalker\Utilities;

/**
 * The StringHelper class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Str
{
    public const CASE_SENSITIVE = true;
    public const CASE_INSENSITIVE = false;

    public const ENCODING_DEFAULT_ISO = 'ISO-8859-1';
    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_US_ASCII = 'US-ASCII';

    /**
     * at
     *
     * @param string $string
     * @param int    $pos
     * @param string $encoding
     *
     * @return string
     */
    public static function getChar(string $string, int $pos, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if (Utf8String::strlen($string, $encoding) < abs($pos)) {
            return '';
        }

        return Utf8String::substr($string, $pos, 1);
    }

    /**
     * between
     *
     * @param string      $string
     * @param string      $start
     * @param string      $end
     * @param int         $offset
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function between(
        string $string,
        string $start,
        string $end,
        int $offset = 0,
        string $encoding = null
    ): string {
        $encoding ??= mb_internal_encoding();

        $startIndex = Utf8String::strpos($string, $start, $offset, $encoding);

        if ($startIndex === false) {
            return '';
        }

        $substrIndex = $startIndex + Utf8String::strlen($start, $encoding);

        $endIndex = Utf8String::strpos($string, $end, $substrIndex, $encoding);

        if ($endIndex === false) {
            return '';
        }

        return Utf8String::substr($string, $substrIndex, $endIndex - $substrIndex);
    }

    /**
     * collapseWhitespaces
     *
     * @param string $string
     *
     * @return  string
     */
    public static function collapseWhitespaces(string $string): string
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

    /**
     * endsWith
     *
     * @param string $string
     * @param string $search
     * @param bool   $caseSensitive
     * @param string $encoding
     *
     * @return bool
     */
    public static function endsWith(
        string $string,
        string $search,
        bool $caseSensitive = true,
        string $encoding = null
    ): bool {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        $stringLength = Utf8String::strlen($string, $encoding);
        $targetLength = Utf8String::strlen($search, $encoding);

        if ($stringLength < $targetLength) {
            return false;
        }

        if (!$caseSensitive) {
            $string = Utf8String::strtoupper($string, $encoding);
            $search = Utf8String::strtoupper($search, $encoding);
        }

        $end = Utf8String::substr($string, -$targetLength, null, $encoding);

        return $end === $search;
    }

    /**
     * startsWith
     *
     * @param string  $string
     * @param string  $target
     * @param boolean $caseSensitive
     * @param string  $encoding
     *
     * @return bool
     */
    public static function startsWith(
        string $string,
        string $target,
        bool $caseSensitive = true,
        string $encoding = null
    ): bool {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if (!$caseSensitive) {
            $string = Utf8String::strtoupper($string, $encoding);
            $target = Utf8String::strtoupper($target, $encoding);
        }

        return Utf8String::strpos($string, $target, 0, $encoding) === 0;
    }

    /**
     * ensureLeft
     *
     * @param string      $string
     * @param string      $search
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function ensureLeft(string $string, string $search, string $encoding = null): string
    {
        if (static::startsWith($string, $search, true, $encoding)) {
            return $string;
        }

        return $search . $string;
    }

    /**
     * ensureRight
     *
     * @param string      $string
     * @param string      $search
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function ensureRight(string $string, string $search, string $encoding = null): string
    {
        if (static::endsWith($string, $search, true, $encoding)) {
            return $string;
        }

        return $string . $search;
    }

    /**
     * hasLowerCase
     *
     * @param string      $string
     * @param string|null $encoding
     *
     * @return  bool
     */
    public static function hasLowerCase(string $string, string $encoding = null): bool
    {
        return static::match('.*[[:lower:]]', $string, 'msr', $encoding);
    }

    /**
     * hasUpperCase
     *
     * @param string      $string
     * @param string|null $encoding
     *
     * @return  bool
     */
    public static function hasUpperCase(string $string, string $encoding = null): bool
    {
        return static::match('.*[[:upper:]]', $string, 'msr', $encoding);
    }

    /**
     * match
     *
     * @param string      $pattern
     * @param string      $string
     * @param string|null $option
     * @param string|null $encoding
     *
     * @return  bool
     */
    public static function match(string $pattern, string $string, string $option = 'msr', string $encoding = null): bool
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        $encodingBackup = mb_regex_encoding();

        mb_regex_encoding($encoding);

        $result = mb_ereg_match($pattern, $string, $option);

        mb_regex_encoding($encodingBackup);

        return $result;
    }

    /**
     * insert
     *
     * @param string      $string
     * @param string      $insert
     * @param int         $position
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function insert(string $string, string $insert, int $position, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        $length = Utf8String::strlen($string, $encoding);

        if ($position > $length) {
            return $string;
        }

        $left  = Utf8String::substr($string, 0, $position, $encoding);
        $right = Utf8String::substr($string, $position, $length, $encoding);

        return $left . $insert . $right;
    }

    /**
     * isLowerCase
     *
     * @param string $string
     *
     * @return  bool
     */
    public static function isLowerCase(string $string): bool
    {
        return static::match('^[[:lower:]]*$', $string);
    }

    /**
     * isUpperCase
     *
     * @param string $string
     *
     * @return  bool
     */
    public static function isUpperCase(string $string): bool
    {
        return static::match('^[[:upper:]]*$', $string);
    }

    /**
     * first
     *
     * @param string      $string
     * @param int         $length
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function first(string $string, int $length = 1, string $encoding = null): string
    {
        if ($string === '' || $length <= 0) {
            return '';
        }

        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        return Utf8String::substr($string, 0, $length, $encoding);
    }

    /**
     * last
     *
     * @param string      $string
     * @param int         $length
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function last(string $string, int $length = 1, string $encoding = null): string
    {
        if ($string === '' || $length <= 0) {
            return '';
        }

        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        return Utf8String::substr($string, -$length, null, $encoding);
    }

    /**
     * intersectLeft
     *
     * @param string      $string1
     * @param string      $string2
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function intersectLeft(string $string1, string $string2, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;
        $maxLength = min(Utf8String::strlen($string1, $encoding), Utf8String::strlen($string2, $encoding));
        $intersect = '';

        for ($i = 0; $i <= $maxLength; $i++) {
            $char = Utf8String::substr($string1, $i, 1, $encoding);

            if ($char === Utf8String::substr($string2, $i, 1, $encoding)) {
                $intersect .= $char;
            } else {
                break;
            }
        }

        return $intersect;
    }

    /**
     * intersectRight
     *
     * @param string      $string1
     * @param string      $string2
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function intersectRight(string $string1, string $string2, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;
        $maxLength = min(Utf8String::strlen($string1, $encoding), Utf8String::strlen($string2, $encoding));
        $intersect = '';

        for ($i = 1; $i <= $maxLength; $i++) {
            $char = Utf8String::substr($string1, -$i, 1, $encoding);

            if ($char === Utf8String::substr($string2, -$i, 1, $encoding)) {
                $intersect = $char . $intersect;
            } else {
                break;
            }
        }

        return $intersect;
    }

    /**
     * intersect
     *
     * @see https://en.wikipedia.org/wiki/Longest_common_substring_problem
     * @see https://en.wikibooks.org/wiki/Algorithm_Implementation/Strings/Longest_common_substring#PHP
     *
     * @param string      $string1
     * @param string      $string2
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function intersect(string $string1, string $string2, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;
        $str1Length = Utf8String::strlen($string1, $encoding);
        $str2Length = Utf8String::strlen($string2, $encoding);

        if ($str1Length === 0 || $str2Length === 0) {
            return '';
        }

        $len = 0;
        $end = 0;

        $subsequence = array_fill(0, $str1Length + 1, array_fill(0, $str2Length + 1, 0));

        for ($i = 1; $i <= $str1Length; $i++) {
            for ($j = 1; $j <= $str2Length; $j++) {
                $str1Char = Utf8String::substr($string1, $i - 1, 1, $encoding);
                $str2Char = Utf8String::substr($string2, $j - 1, 1, $encoding);

                if ($str1Char === $str2Char) {
                    $subsequence[$i][$j] = $subsequence[$i - 1][$j - 1] + 1;

                    if ($subsequence[$i][$j] > $len) {
                        $len = $subsequence[$i][$j];
                        $end = $i;
                    }
                } else {
                    $subsequence[$i][$j] = 0;
                }
            }
        }

        return Utf8String::substr($string1, $end - $len, $len, $encoding);
    }

    /**
     * pad
     *
     * @param string      $string
     * @param int         $length
     * @param string      $substring
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function pad(
        string $string,
        int $length = 0,
        string $substring = ' ',
        string $encoding = null
    ): string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;
        $strLength = Utf8String::strlen($string, $encoding);
        $padding   = $length - $strLength;

        return static::doPad($string, (int) floor($padding / 2), (int) ceil($padding / 2), $substring, $encoding);
    }

    /**
     * padLeft
     *
     * @param string      $string
     * @param int         $length
     * @param string      $substring
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function padLeft(
        string $string,
        int $length = 0,
        string $substring = ' ',
        string $encoding = null
    ): string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        return static::doPad($string, $length - Utf8String::strlen($string, $encoding), 0, $substring, $encoding);
    }

    /**
     * padRight
     *
     * @param string      $string
     * @param int         $length
     * @param string      $substring
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function padRight(
        string $string,
        int $length = 0,
        string $substring = ' ',
        string $encoding = null
    ): string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        return static::doPad($string, 0, $length - Utf8String::strlen($string, $encoding), $substring, $encoding);
    }

    /**
     * doPad
     *
     * @param string      $string
     * @param int         $left
     * @param int         $right
     * @param string      $substring
     * @param string|null $encoding
     *
     * @return  string
     */
    private static function doPad(
        string $string,
        int $left,
        int $right,
        string $substring,
        string $encoding = null
    ): string {
        $strLength = Utf8String::strlen($string, $encoding);
        $padLength = Utf8String::strlen($substring, $encoding);
        $paddedLength = $strLength + $left + $right;

        if (!$padLength || $paddedLength <= $strLength) {
            return $string;
        }

        $leftStr = Utf8String::substr(str_repeat($substring, (int) ceil($left / $padLength)), 0, $left, $encoding);
        $rightStr = Utf8String::substr(str_repeat($substring, (int) ceil($right / $padLength)), 0, $right, $encoding);

        return $leftStr . $string . $rightStr;
    }

    /**
     * removeChar
     *
     * @param string      $string
     * @param int         $offset
     * @param int|null    $length
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function removeChar(string $string, int $offset, int $length = null, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if (Utf8String::strlen($string, $encoding) < abs($offset)) {
            return $string;
        }

        $length = $length === null ? 1 : $length;

        return Utf8String::substrReplace($string, '', $offset, $length, $encoding);
    }

    /**
     * removeLeft
     *
     * @param string      $string
     * @param string      $search
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function removeLeft(string $string, string $search, string $encoding = null): string
    {
        if ($string === '') {
            return '';
        }

        if ($search === '') {
            return $string;
        }

        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if (!static::startsWith($string, $search, true, $encoding)) {
            return $string;
        }

        return Utf8String::substr($string, Utf8String::strlen($search), null, $encoding);
    }

    /**
     * removeRight
     *
     * @param string      $string
     * @param string      $search
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function removeRight(string $string, string $search, string $encoding = null): string
    {
        if ($string === '') {
            return '';
        }

        if ($search === '') {
            return $string;
        }

        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if (!static::endsWith($string, $search, true, $encoding)) {
            return $string;
        }

        return Utf8String::substr($string, 0, -Utf8String::strlen($search), $encoding);
    }

    /**
     * slice
     *
     * @param string      $string
     * @param int         $start
     * @param int|null    $end
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function slice(string $string, int $start, int $end = null, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if ($end === null) {
            $length = Utf8String::strlen($string, $encoding);
        } elseif ($end >= 0 && $end <= $start) {
            return '';
        } elseif ($end < 0) {
            $length = Utf8String::strlen($string, $encoding) + $end - $start;
        } else {
            $length = $end - $start;
        }

        return Utf8String::substr($string, $start, $length, $encoding);
    }

    /**
     * substring
     *
     * @param string      $string
     * @param int         $start
     * @param int|null    $end
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function substring(string $string, int $start, int $end = null, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if ($end === null) {
            $length = Utf8String::strlen($string, $encoding);
        } elseif ($end >= 0 && $end <= $start) {
            $length = $start - $end;
            $start = $end;
        } elseif ($end < 0) {
            $length = Utf8String::strlen($string, $encoding) + $end - $start;
        } else {
            $length = $end - $start;
        }

        return Utf8String::substr($string, $start, $length, $encoding);
    }

    /**
     * surround
     *
     * @param string        $string
     * @param string|array  $substring
     *
     * @return  string
     */
    public static function surround(string $string, $substring = ['"', '"']): string
    {
        $substring = (array) $substring;

        if (empty($substring[1])) {
            $substring[1] = $substring[0];
        }

        return $substring[0] . $string . $substring[1];
    }

    /**
     * toggleCase
     *
     * @param string      $string
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function toggleCase(string $string, string $encoding = null): string
    {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        return preg_replace_callback(
            '/[\S]/u',
            function ($match) use ($encoding) {
                if ($match[0] === Utf8String::strtoupper($match[0], $encoding)) {
                    return Utf8String::strtolower($match[0], $encoding);
                }

                return Utf8String::strtoupper($match[0], $encoding);
            },
            $string
        );
    }

    /**
     * truncate
     *
     * @param string      $string
     * @param int         $length
     * @param string      $suffix
     * @param bool        $wordBreak
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function truncate(
        string $string,
        int $length,
        string $suffix = '',
        bool $wordBreak = true,
        ?string $encoding = null
    ): string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;

        if ($length >= Utf8String::strlen($string, $encoding)) {
            return $string;
        }

        $result = Utf8String::substr($string, 0, $length, $encoding);

        if (!$wordBreak && Utf8String::strpos($result, ' ', 0, $encoding) !== $length) {
            $position = Utf8String::strrpos($result, ' ', 0, $encoding);
            $result = Utf8String::substr($result, 0, $position, $encoding);
        }

        return $result . $suffix;
    }

    /**
     * map
     *
     * @param string      $string
     * @param callable    $callback
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function map(string $string, callable $callback, string $encoding = null): string
    {
        $result = [];

        foreach (Utf8String::strSplit($string, 1, $encoding) as $key => $char) {
            if ($callback instanceof \Closure) {
                $result[] = $callback($char, $key);
            } else {
                $result[] = $callback($char);
            }
        }

        return implode('', $result);
    }

    /**
     * filter
     *
     * @param string      $string
     * @param callable    $callback
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function filter(string $string, callable $callback, string $encoding = null): string
    {
        return static::map($string, function ($char, &$key) use ($callback) {
            if ($callback instanceof \Closure) {
                $result = $callback($char, $key);
            } else {
                $result = $callback($char);
            }

            return $result ? $char : '';
        }, $encoding);
    }

    /**
     * reject
     *
     * @param string      $string
     * @param callable    $callback
     * @param string|null $encoding
     *
     * @return  string
     */
    public static function reject(string $string, callable $callback, string $encoding = null): string
    {
        return static::filter($string, function ($char, &$key) use ($callback) {
            if ($callback instanceof \Closure) {
                $result = $callback($char, $key);
            } else {
                $result = $callback($char);
            }

            return !$result;
        }, $encoding);
    }
}
