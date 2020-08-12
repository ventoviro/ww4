<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Session\Handler;

use Windwalker\Session\Cookies;

/**
 * Class AbstractHandler
 *
 * @since 2.0
 */
abstract class AbstractHandler implements HandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    protected ?string $loadedData = null;

    protected bool $newSessionId = false;

    /**
     * Re-initializes existing session, or creates a new one.
     *
     * @param  string  $savePath     Save path
     * @param  string  $sessionName  Session name, see http://php.net/function.session-name.php
     *
     * @return bool true on success, false on failure
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Closes the current session.
     *
     * @return bool true on success, false on failure
     */
    public function close()
    {
        return true;
    }

    /**
     * validateId
     *
     * @param  string  $id
     *
     * @return  bool
     */
    public function validateId($id)
    {
        $this->loadedData = $this->read($id);

        $newSessionId = $this->newSessionId;

        $this->newSessionId = false;

        return !$newSessionId;
    }

    /**
     * read
     *
     * @param  string  $id
     *
     * @return  string
     */
    public function read($id)
    {
        $data = $this->loadedData;

        if ($data !== null) {
            $this->loadedData = null;
            return $data;
        }

        $data = $this->doRead($id);

        if ($data === null) {
            $this->newSessionId = true;
        }

        return (string) $data;
    }

    abstract public function doRead(string $id): ?string;
}
