<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Handler;

/**
 * The StorageHandler class.
 */
class StorageHandler extends AbstractHandler
{


    /**
     * doRead
     *
     * @param  string  $id
     *
     * @return  string|null
     */
    public function doRead(string $id): ?string
    {
    }

    /**
     * isSupported
     *
     * @return  bool
     */
    public static function isSupported(): bool
    {
    }

    /**
     * destroy
     *
     * @param  string  $session_id
     *
     * @return  bool
     */
    public function destroy($session_id)
    {
    }

    /**
     * gc
     *
     * @param  int  $maxlifetime
     *
     * @return  bool
     */
    public function gc($maxlifetime)
    {
    }

    /**
     * write
     *
     * @param  string  $session_id
     * @param  string  $session_data
     *
     * @return  bool
     */
    public function write($session_id, $session_data)
    {
    }

    /**
     * updateTimestamp
     *
     * @param  string  $session_id
     * @param  string  $session_data
     *
     * @return  bool
     */
    public function updateTimestamp($session_id, $session_data)
    {
    }
}
