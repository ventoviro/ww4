<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Bridge;

use Windwalker\Session\CookieSetter;

/**
 * The PhpBridge class.
 */
class PhpBridge implements BridgeInterface
{
    /**
     * @var CookieSetter
     */
    protected CookieSetter $cookieSetter;

    /**
     * NativeBridge constructor.
     *
     * @param  CookieSetter|null  $cookieSetter
     */
    public function __construct(CookieSetter $cookieSetter = null)
    {
        $this->cookieSetter = $cookieSetter ?? CookieSetter::create()
            ->httpOnly(true)
            ->expires('+30days')
            ->secure(false)
            ->sameSite(CookieSetter::SAMESITE_LAX);
    }

    /**
     * start
     *
     * @return  bool
     */
    public function start(): bool
    {
        if ($this->isStarted()) {
            return true;
        }

        $this->setCookieParams();

        // Call session_write_close when shutdown.
        session_register_shutdown();

        if (!headers_sent()) {
            session_cache_limiter('private');
        }

        session_start();

        return true;
    }

    /**
     * isStarted
     *
     * @return  bool
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * getId
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return session_id();
    }

    /**
     * setId
     *
     * @param  string  $id
     *
     * @return void
     */
    public function setId(string $id): void
    {
        session_id($id);
    }

    /**
     * getSessionName
     *
     * @return  string|null
     */
    public function getSessionName(): ?string
    {
        return session_name();
    }

    /**
     * setSessionName
     *
     * @param  string  $name
     *
     * @return  void
     */
    public function setSessionName(string $name): void
    {
        session_name($name);
    }

    /**
     * restart
     *
     * @param  bool  $deleteOld
     *
     * @return  mixed
     */
    public function restart(bool $deleteOld = false): bool
    {
        $return = $this->regenerate($deleteOld);

        $this->start();

        return $return;
    }

    /**
     * regenerate
     *
     * @param  bool  $deleteOld
     *
     * @return  bool
     */
    public function regenerate(bool $deleteOld = false): bool
    {
        return session_regenerate_id($deleteOld);
    }

    /**
     * save
     *
     * @param  bool  $unset
     *
     * @return bool
     */
    public function writeClose(bool $unset = true): bool
    {
        $result = session_write_close();

        if ($unset) {
            $_SESSION = [];
        }

        return $result;
    }

    /**
     * destroy
     *
     * @return  void
     */
    public function destroy(): void
    {
        if ($this->getId()) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * getCookieParams
     *
     * @return  array
     */
    public function getCookieParams(): array
    {
        return session_get_cookie_params();
    }

    /**
     * Set session cookie parameters, this method should call before session started.
     *
     * @param array $options An associative array which may have any of the keys lifetime, path, domain,
     * secure, httponly and samesite. The values have the same meaning as described
     * for the parameters with the same name. The value of the samesite element
     * should be either Lax or Strict. If any of the allowed options are not given,
     * their default values are the same as the default values of the explicit
     * parameters. If the samesite element is omitted, no SameSite cookie attribute
     * is set.
     *
     * @since   2.0
     */
    public function setCookieParams(?array $options = null): void
    {
        if (headers_sent()) {
            session_set_cookie_params($options ?? $this->cookieSetter->getOptions());
        }
    }

    /**
     * getStorage
     *
     * @return array|null
     */
    public function &getStorage(): ?array
    {
        return $_SESSION;
    }
}
