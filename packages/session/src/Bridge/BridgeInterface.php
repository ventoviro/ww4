<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Session\Bridge;

/**
 * Interface SessionBridgeInterface
 *
 * @since  2.0
 */
interface BridgeInterface
{
    /**
     * Starts the session.
     *
     * @return  bool  True if started.
     *
     * @throws \RuntimeException If something goes wrong starting the session.
     */
    public function start();

    /**
     * Checks if the session is started.
     *
     * @return  bool  True if started, false otherwise.
     */
    public function isStarted();

    /**
     * Returns the session ID
     *
     * @return  string  The session ID or empty.
     */
    public function getId();

    /**
     * Sets the session ID
     *
     * @param   string $id Set the session id
     *
     * @return  void
     */
    public function setId($id);

    /**
     * Returns the session name
     *
     * @return  mixed   The session name.
     */
    public function getName();

    /**
     * Sets the session name
     *
     * @param   string $name Set the name of the session
     *
     * @return  void
     */
    public function setName($name);

    /**
     * Regenerates id that represents this storage.
     *
     * This method must invoke session_regenerate_id($destroy) unless
     * this interface is used for a storage object designed for unit
     * or functional testing where a real PHP session would interfere
     * with testing.
     *
     * Note regenerate+destroy should not clear the session data in memory
     * only delete the session data from persistent storage.
     *
     * @param   bool $destroy    Destroy session when regenerating?
     * @param   int  $lifetime   Sets the cookie lifetime for the session cookie. A null value
     *                           will leave the system settings unchanged, 0 sets the cookie
     *                           to expire with browser session. Time is in seconds, and is
     *                           not a Unix timestamp.
     *
     * @return  bool  True if session regenerated, false if error
     *
     * @throws  \RuntimeException  If an error occurs while regenerating this storage
     */
    public function restart($destroy = false, $lifetime = null);

    /**
     * regenerate
     *
     * @param bool $destroy
     *
     * @return  bool
     */
    public function regenerate($destroy = false);

    /**
     * Force the session to be saved and closed.
     *
     * This method must invoke session_write_close() unless this interface is
     * used for a storage object design for unit or functional testing where
     * a real PHP session would interfere with testing, in which case it
     * it should actually persist the session data if required.
     *
     * @return  void
     *
     * @throws \RuntimeException If the session is saved without being started, or if the session
     *                           is already closed.
     */
    public function save();

    /**
     * Clear all session data in memory.
     *
     * @return  void
     */
    public function destroy();

    /**
     * getCookieParams
     *
     * @return  array
     */
    public function getCookieParams();

    /**
     * Set session cookie parameters, this method should call before session started.
     *
     * @param   integer $lifetime   Lifetime of the session cookie, defined in seconds.
     * @param   string  $path       Path on the domain where the cookie will work. Use a single
     *                              slash ('/') for all paths on the domain.
     * @param   string  $domain     Cookie domain, for example 'www.php.net'. To make cookies
     *                              visible on all sub domains then the domain must be prefixed
     *                              with a dot like '.php.net'.
     * @param   boolean $secure     If true cookie will only be sent over secure connections.
     * @param   boolean $httponly   If set to true then PHP will attempt to send the httponly
     *                              flag when setting the session cookie.
     *
     * @return  static
     *
     * @since   2.0
     */
    public function setCookieParams($lifetime, $path = null, $domain = null, $secure = false, $httponly = true);

    /**
     * getStorage
     *
     * @return  array
     */
    public function &getStorage();
}
