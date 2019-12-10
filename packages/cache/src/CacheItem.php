<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Cache;

use DateTimeInterface;
use Psr\Cache\CacheItemInterface;
use Windwalker\Cache\Exception\InvalidArgumentException;

/**
 * Class CacheItem
 *
 * @since 2.0
 */
class CacheItem implements CacheItemInterface
{
    /**
     * The key for the cache item.
     *
     * @var    string
     * @since  2.0
     */
    protected $key;

    /**
     * The value of the cache item.
     *
     * @var    mixed
     * @since  2.0
     */
    protected $value;

    /**
     * Whether the cache item is value or not.
     *
     * @var    boolean
     * @since  2.0
     */
    protected $hit = false;

    /**
     * Property expiration.
     *
     * @var  DateTimeInterface
     */
    protected $expiration;

    /**
     * Property defaultExpiration.
     *
     * @var  string
     */
    protected $defaultExpiration = 'now +1 year';

    /**
     * @var callable
     */
    protected $getter;

    /**
     * Class constructor.
     *
     * @param  string         $key  The key for the cache item.
     * @param  callable|null  $getter
     *
     * @since   2.0
     */
    public function __construct(?string $key = null, ?callable $getter = null)
    {
        $this->validateKey($key);

        $this->key = $key;
        $this->getter = $getter;

        $this->expiresAfter(null);
    }

    /**
     * Get the key associated with this CacheItem.
     *
     * @return  string
     *
     * @since   2.0
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Obtain the value of this cache item.
     *
     * @return  mixed
     *
     * @throws \Exception
     * @since   2.0
     */
    public function get()
    {
        if ($this->isHit() === false) {
            return null;
        }

        if ($this->value !== null) {
            return $this->value;
        }

        return $this->getGetter()();
    }

    /**
     * Set the value of the item.
     *
     * If the value is set, we are assuming that there was a valid hit on the cache for the given key.
     *
     * @param  mixed  $value  The value for the cache item.
     *
     * @return  static
     */
    public function set($value)
    {
        if ($this->key === null) {
            return $this;
        }

        $this->value = $value;
        $this->hit   = true;

        return $this;
    }

    /**
     * This boolean value tells us if our cache item is currently in the cache or not.
     *
     * @return  boolean
     *
     * @since   2.0
     */
    public function isHit()
    {
        if (new \DateTime() > $this->expiration) {
            $this->hit = false;
        }

        return $this->hit;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param  DateTimeInterface  $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof DateTimeInterface) {
            $this->expiration = $expiration;
        } elseif ($expiration === null) {
            $this->expiration = new \DateTime($this->defaultExpiration);
        } else {
            throw new \InvalidArgumentException('Invalid DateTime format.');
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param  int|\DateInterval  $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $this->expiration = new \DateTime();
            $this->expiration->add($time);
        } elseif (is_numeric($time)) {
            $this->expiration = new \DateTime();
            $this->expiration->add(new \DateInterval('PT' . $time . 'S'));
        } elseif ($time === null) {
            $this->expiration = new \DateTime($this->defaultExpiration);
        } else {
            throw new InvalidArgumentException('Invalid DateTime format.');
        }

        return $this;
    }

    /**
     * Method to get property Expiration
     *
     * @return  DateTimeInterface
     */
    public function getExpiration(): DateTimeInterface
    {
        return $this->expiration;
    }

    /**
     * validateKey
     *
     * @param  string  $key
     *
     * @return  void:
     */
    private function validateKey(string $key): void
    {
        if (strpbrk($key, '{}()/\@:')) {
            throw new InvalidArgumentException('Item key name contains reserved characters.' . $key);
        }
    }

    /**
     * Method to get property Getter
     *
     * @return  callable
     */
    public function getGetter(): callable
    {
        return $this->getter;
    }

    /**
     * Method to set property getter
     *
     * @param  callable  $getter
     *
     * @return  static  Return self to support chaining.
     */
    public function setGetter(callable $getter)
    {
        $this->getter = $getter;

        return $this;
    }
}
