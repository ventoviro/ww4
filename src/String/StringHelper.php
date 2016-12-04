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
    public const CASE_SENSITIVE = true;
    public const CASE_INSENSITIVE = false;

    /**
     * at
     *
     * @param string $string
     * @param int    $pos
     *
     * @return  string
     */
    public static function at(string $string, int $pos)
    {
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

    /**
     * collapseWhitespaces
     *
     * @param string $string
     *
     * @return  string
     */
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
    ) : bool {
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
    ) : bool {
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
    ) : bool {
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
    public static function ensureLeft(string $string, string $search, string $encoding = null) : string
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
    public static function ensureRight(string $string, string $search, string $encoding = null) : string
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
    public static function hasLowerCase(string $string, string $encoding = null) : bool
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
    public static function hasUpperCase(string $string, string $encoding = null) : bool
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
}
