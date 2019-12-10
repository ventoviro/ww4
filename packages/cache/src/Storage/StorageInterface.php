<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

/**
 * The StorageInterface class.
 */
interface StorageInterface
{
    /**
     * get
     *
     * @param  string  $key
     * @param  array   $options
     *
     * @return  mixed
     */
    public function get(string $key, array $options = []);

    /**
     * has
     *
     * @param  string  $key
     *
     * @return  bool
     */
    public function has(string $key): bool;

    /**
     * clear
     *
     * @return  void
     */
    public function clear(): void;

    /**
     * remove
     *
     * @param  string  $key
     *
     * @return  void
     */
    public function remove(string $key): void;

    /**
     * save
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  array   $options
     *
     * @return  void
     */
    public function save(string $key, $value, array $options = []): void;
}
