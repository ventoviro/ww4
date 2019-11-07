<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Contract;

/**
 * Interface DumpableInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface DumpableInterface
{
    /**
     * Dump to array.
     *
     * @param  bool  $recursive     Dump children array.
     * @param  bool  $onlyDumpable  Objects only implements DumpableInterface will convert to array.
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function dump(bool $recursive = false, bool $onlyDumpable = false): array;
}
