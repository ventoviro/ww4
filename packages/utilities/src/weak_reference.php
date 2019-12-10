<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

class WeakReference
{
    /**
     * @var object|null
     */
    private $referent;

    /**
     * This method exists only to disallow instantiation of the WeakReference
     * class. Weak references are to be instantiated with the factory method
     * <b>WeakReference::create()</b>.
     */
    protected function __construct()
    {
        //
    }

    /**
     * Create a new weak reference.
     * @link https://www.php.net/manual/en/weakreference.create.php
     * @param object $referent The object to be weakly referenced.
     * @return WeakReference the freshly instantiated object.
     * @since 7.4.0
     */
    public static function create(object $referent): WeakReference
    {
        $instance = new static();
        $instance->referent = $referent;

        return $instance;
    }

    /**
     * Gets a weakly referenced object. If the object has already been
     * destroyed, NULL is returned.
     * @link https://www.php.net/manual/en/weakreference.get.php
     * @return object|null
     * @since 7.4.0
     */
    public function get(): ?object
    {
        return $this->referent;
    }
}
