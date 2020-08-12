<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Format;

use Windwalker\Data\Format\FormatInterface;

use function Windwalker\tap;

/**
 * The SessionSerializeFormat class.
 */
class SessionSerializeFormat implements FormatInterface
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
        $backup = $_SESSION;
        $_SESSION = $data;

        return tap(session_encode(), fn () => ($_SESSION = $backup));
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
        session_decode($string);

        return $_SESSION;
    }
}
