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
use Windwalker\Session\Bridge\PhpBridge;
use Windwalker\Session\Handler\HandlerInterface;
use Windwalker\Session\Handler\NativeHandler;
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
    protected ?Cookies $cookies;

    /**
     * Session constructor.
     *
     * @param  array                  $options
     * @param  BridgeInterface|null   $bridge
     */
    public function __construct(array $options = [], ?BridgeInterface $bridge = null, ?Cookies $cookies = null)
    {
        $this->bridge  = $bridge ?? new PhpBridge();
        $this->cookies = $cookies ?? Cookies::create()
            ->httpOnly(true)
            ->expires('+30days')
            ->secure(false)
            ->sameSite(Cookies::SAMESITE_LAX);

        $this->prepareOptions(
            [
                'ini' => [
                    //
                ]
            ],
            $options
        );
    }

    public function registerINI(): void
    {
        if (!headers_sent()) {
            foreach ($this->getOption('ini') as $key => $value) {
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

        if (ini_get('session.use_cookies')) {
            $this->setCookieParams();
        } else {
            $this->bridge->setId(
                $this->cookies->get(
                    $this->bridge->getSessionName()
                )
            );

            register_shutdown_function(function () {
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
        return $this->bridge->getStorage();
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
}
