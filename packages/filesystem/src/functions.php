<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker;

use Windwalker\Filesystem\Path;

/**
 * Support node style double star finder.
 *
 * ```
 * \Windwalker\glob('/var/www/foo/**\/*.php')
 * ```
 *
 * @param  string  $pattern
 * @param  int     $flags
 *
 * @return  array
 */
function glob(string $pattern, int $flags = 0): array
{
    $pattern = Path::clean($pattern);

    if (strpos($pattern, '**') === false) {
        $files = \glob($pattern, $flags);
    } else {
        $position = strpos($pattern, '**');
        $rootPattern = substr($pattern, 0, $position - 1);
        $restPattern = substr($pattern, $position + 2);
        $patterns = [$rootPattern . $restPattern];
        $rootPattern .= DIRECTORY_SEPARATOR . '*';

        while ($dirs = \glob($rootPattern, GLOB_ONLYDIR)) {
            $rootPattern .= DIRECTORY_SEPARATOR . '*';

            foreach ($dirs as $dir) {
                $patterns[] = $dir . $restPattern;
            }
        }

        $files = [];

        foreach ($patterns as $pat) {
            $files[] = glob($pat, $flags);
        }

        $files = array_merge(...$files);
    }

    $files = array_unique($files);

    sort($files);

    return $files;
}

/**
 * glob_all
 *
 * @param  string  $baseDir
 * @param  array   $patterns
 * @param  int     $flags
 *
 * @return  array
 */
function glob_all(string $baseDir, array $patterns, int $flags = 0): array
{
    $files = [];
    $inverse = [];

    foreach ($patterns as $pattern) {
        if (strpos($pattern, '!') === 0) {
            $pattern = substr($pattern, 1);

            $inverse[] = glob(
                rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                $flags
            );
        } else {
            $files[] = glob(
                rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                $flags
            );
        }
    }

    if ($files !== []) {
        $files = array_unique(array_merge(...$files));
    }

    if ($inverse !== []) {
        $inverse = array_unique(array_merge(...$inverse));
    }

    return array_diff($files, $inverse);
}
