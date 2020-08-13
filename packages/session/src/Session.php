<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session;

use Windwalker\Session\Bridge\BridgeInterface;
use Windwalker\Session\Bridge\NativeBridge;
use Windwalker\Session\Cookie\Cookies;
use Windwalker\Session\Cookie\CookiesInterface;
use Windwalker\Session\Handler\HandlerInterface;
use Windwalker\Utilities\Accessible\SimpleAccessibleTrait;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The Session class.
 */
class Session implements SessionInterface
{
    use OptionAccessTrait;
    use SimpleAccessibleTrait;

    protected ?HandlerInterface $handler = null;

    protected ?BridgeInterface $bridge = null;

    /**
     * @var Cookies|null
     */
    protected ?CookiesInterface $cookies;

    protected ?FlashBag $flashBag = null;

    /**
     * Session constructor.
     *
     * @param  array                 $options
     * @param  BridgeInterface|null  $bridge
     * @param  CookiesInterface|null          $cookies
     */
    public function __construct(array $options = [], ?BridgeInterface $bridge = null, ?CookiesInterface $cookies = null)
    {
        $this->prepareOptions(
            [
                'auto_commit' => true,
                'ini' => [
                    //
                ]
            ],
            $options
        );

        $this->bridge  = $bridge ?? new NativeBridge();
        $this->cookies = $cookies ?? Cookies::create()
            ->httpOnly(true)
            ->expires('+30days')
            ->secure(false)
            ->sameSite(Cookies::SAMESITE_LAX);
    }

    public function registerINI(): void
    {
        if (!headers_sent()) {
            foreach ((array) $this->getOption('ini') as $key => $value) {
                if ($value !== null) {
                    if (!str_starts_with($key, 'session.')) {
                        $key = 'session.' . $key;
                    }

                    ini_set($key, $value);
                }
            }
        }
    }

    public function setName(string $name)
    {
        $this->bridge->setSessionName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->bridge->getSessionName();
    }

    public function start(): bool
    {
        if ($this->bridge->isStarted()) {
            return true;
        }

        $this->registerINI();

        if ($this->getOptionAndINI('use_cookies')) {
            // If use auto cookie, we set cookie params first.
            $this->setCookieParams();
        } else {
            // Otherwise set session ID from $_COOKIE.
            $this->bridge->setId(
                $this->cookies->get(
                    $this->bridge->getSessionName()
                )
            );

            // Must set cookie and update expires after session end.
            register_shutdown_function(function () {
                if ($this->getOption('auto_commit')) {
                    $this->stop(true);
                }

                $this->cookies->set(
                    $this->bridge->getSessionName(),
                    $this->bridge->getId()
                );
            });
        }

        return $this->bridge->start();
    }

    public function stop(bool $unset = true): bool
    {
        return $this->bridge->writeClose($unset);
    }

    public function fork(): bool
    {
        return $this->bridge->regenerate();
    }

    public function restart(): bool
    {
        return $this->bridge->regenerate(true);
    }

    /**
     * clear
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->bridge->unset();
    }

    public function &all(): array
    {
        return $this->getStorage();
    }

    /**
     * count
     *
     * @return  int
     */
    public function count()
    {
        return \Windwalker\count($this->getStorage());
    }

    /**
     * jsonSerialize
     *
     * @return  mixed
     */
    public function jsonSerialize()
    {
        return $this->bridge->getStorage();
    }

    public function &getStorage(): ?array
    {
        $storage =& $this->bridge->getStorage();

        return $storage;
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
            session_set_cookie_params($options ?? $this->cookies->getOptions());
        }
    }

    /**
     * @return Cookies|null
     */
    public function getCookies(): ?Cookies
    {
        return $this->cookies;
    }

    /**
     * @param  Cookies|null  $cookies
     *
     * @return  static  Return self to support chaining.
     */
    public function setCookies(?Cookies $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    protected function getOptionAndINI(string $name)
    {
        return $this->getOption($name) ?? ini_get('session.' . $name);
    }

    /**
     * @return FlashBag
     */
    public function getFlashBag(): FlashBag
    {
        if ($this->flashBag === null) {
            $storage = &$this->getStorage();
            $storage['_flash'] = [];

            $this->flashBag = new FlashBag($storage['_flash']);
        }

        return $this->flashBag;
    }

    /**
     * @param  FlashBag|null  $flashBag
     *
     * @return  static  Return self to support chaining.
     */
    public function setFlashBag(?FlashBag $flashBag)
    {
        $this->flashBag = $flashBag;

        return $this;
    }

    /**
     * Add a flash message.
     *
     * @param array|string  $messages  The message you want to set, can be an array to storage multiple messages.
     * @param string        $type      The message type, default is `info`.
     *
     * @return  static
     *
     * @since   2.0
     */
    public function addFlash(array|string $messages, string $type = 'info')
    {
        foreach ((array) $messages as $message) {
            $this->getFlashBag()->add($message, $type);
        }

        return $this;
    }

    /**
     * Take all flashes and clean them from bag.
     *
     * @return  array  All flashes data.
     *
     * @since   2.0
     */
    public function getFlashes()
    {
        return $this->getFlashBag()->all();
    }
}
