<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Data\Format;

use Windwalker\Data\DataHelper;

/**
 * PHP class format handler for Data
 *
 * @since  2.0
 */
class PhpFormat implements FormatInterface
{
    /**
     * Converts an object into a php class string.
     * - NOTE: Only one depth level is supported.
     *
     * @param  object  $data     Data Source Object
     * @param  array   $options  Parameters used by the formatter
     *
     * @return  string
     */
    public function dump($data, array $options = []): string
    {
        $header  = $options['header'] ?? '';
        $asArray = $options['return'] ?? false;
        $strict  = $options['strict'] ?? true;

        // Build the object variables string
        $vars = '';

        foreach ($data as $k => $v) {
            if (is_scalar($v)) {
                $vars .= sprintf("    '%s' => '%s',\n", $k, addcslashes($v, '\\\''));
            } elseif (is_array($v) || is_object($v)) {
                $vars .= sprintf("    '%s' => %s,\n", $k, static::getArrayString((array) $v));
            }
        }

        if (!$asArray) {
            if ($strict) {
                $str = "<?php\n";
            } else {
                $str = "<?php\n";
            }

            if ($header) {
                $str .= $header . "\n";
            }

            $str .= "\nreturn [\n";
        } else {
            $str = "[\n";
        }

        $str .= $vars;
        $str .= ']';

        if (!$asArray) {
            $str .= ";\n";

            // Use the closing tag if set to true in parameters.
            if ($options['closingtag'] ?? false) {
                $str .= "\n?>";
            }
        }

        return $str;
    }

    /**
     * Parse a PHP class formatted string and convert it into an object.
     *
     * @param  string  $string   PHP Class formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return array Data array.
     */
    public function parse(string $string, array $options = []): array
    {
        return $string;
    }

    /**
     * Method to get an array as an exported string.
     *
     * @param  array  $a  The array to get as a string.
     *
     * @return  string
     */
    protected static function getArrayString($a, $level = 2)
    {
        $s = "[\n";
        $i = 0;

        $assoc = static::isAssociative($a);

        foreach ($a as $k => $v) {
            $s .= $i ? ",\n" : '';
            $s .= str_repeat('    ', $level);

            if ($assoc) {
                $s .= "'" . $k . "' => ";
            }

            if (is_array($v) || is_object($v)) {
                $s .= static::getArrayString((array) $v, $level + 1);
            } else {
                $s .= "'" . addslashes($v) . "'";
            }

            $i++;
        }

        $s .= "\n" . str_repeat('    ', $level - 1) . ']';

        return $s;
    }

    /**
     * isAssociative
     *
     * @param  array  $array
     *
     * @return  bool
     *
     * @since  3.5.14
     */
    private static function isAssociative(array $array): bool
    {
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v) {
                return true;
            }
        }

        return false;
    }
}
