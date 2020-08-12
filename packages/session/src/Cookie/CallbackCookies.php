<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Cookie;

/**
 * The CallbackCookies class.
 */
class CallbackCookies implements CookiesInterface
{
    /**
     * @var callable
     */
    protected $getter;

    /**
     * @var callable
     */
    protected $setter;

    /**
     * CallbackCookies constructor.
     *
     * @param  callable|null  $getter
     * @param  callable|null  $setter
     */
    public function __construct(?callable $getter = null, ?callable $setter = null)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }

    /**
     * set
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return  bool
     */
    public function set(string $name, string $value): bool
    {
        $setter = $this->getSetter();

        if (!$setter) {
            return false;
        }

        return (bool) $setter($name, $value);
    }

    /**
     * get
     *
     * @param  string  $name
     *
     * @return  string|null
     */
    public function get(string $name): ?string
    {
        $getter = $this->getGetter();

        if (!$getter) {
            return null;
        }

        return $getter($name);
    }

    /**
     * @return callable
     */
    public function getGetter(): callable
    {
        return $this->getter;
    }

    /**
     * @param  callable  $getter
     *
     * @return  static  Return self to support chaining.
     */
    public function setGetter(callable $getter)
    {
        $this->getter = $getter;

        return $this;
    }

    /**
     * @return callable
     */
    public function getSetter(): callable
    {
        return $this->setter;
    }

    /**
     * @param  callable  $setter
     *
     * @return  static  Return self to support chaining.
     */
    public function setSetter(callable $setter)
    {
        $this->setter = $setter;

        return $this;
    }
}
