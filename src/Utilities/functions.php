<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

if (!function_exists('show')) {
    /**
     * Dump Array or Object as tree node. If send multiple params in this method, this function will batch print it.
     *
     * @param   mixed $data Array or Object to dump.
     *
     * @since   2.0
     *
     * @return  void
     */
    function show($data)
    {
        $args = func_get_args();

        $last = array_pop($args);

        if (is_int($last)) {
            $level = $last;
        } else {
            $level = 5;

            $args[] = $last;
        }

        echo "\n\n";

        if (PHP_SAPI !== 'cli') {
            echo '<pre>';
        }

        // Dump Multiple values
        if (count($args) > 1) {
            $prints = array();

            $i = 1;

            foreach ($args as $arg) {
                $prints[] = "[Value " . $i . "]\n" . \Windwalker\Utilities\ArrayHelper::dump($arg, $level);
                $i++;
            }

            echo implode("\n\n", $prints);
        } else {
            // Dump one value.
            echo \Windwalker\Utilities\ArrayHelper::dump($data, $level);
        }

        if (PHP_SAPI !== 'cli') {
            echo '</pre>';
        }
    }
}
