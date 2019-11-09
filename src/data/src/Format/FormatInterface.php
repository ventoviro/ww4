<?php declare(strict_types=1);

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Data\Format;

/**
 * Class DataFormatInterface
 *
 * @since 2.0
 */
interface FormatInterface
{
    /**
     * Converts an object into a formatted string.
     *
     * @param  array|object  $data     Data Source Object.
     * @param  array         $options  An array of options for the formatter.
     *
     * @return  string  Formatted string.
     *
     * @since   2.0
     */
    public function dump($data, array $options = []): string;

    /**
     * Converts a formatted string into an object.
     *
     * @param  string  $string   Formatted string
     * @param  array   $options  An array of options for the formatter.
     *
     * @return array Data array
     *
     * @since   2.0
     */
    public function parse(string $string, array $options = []): array;
}
