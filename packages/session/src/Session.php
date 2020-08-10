<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session;

use Windwalker\Utilities\Accessible\SimpleAccessibleTrait;

/**
 * The Session class.
 */
class Session implements SessionInterface
{
    use SimpleAccessibleTrait;

    protected array $origin = [];

    protected $handler;

    /**
     * clear
     *
     * @return  void
     */
    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * hasChanged
     *
     * @return  bool
     */
    public function hasChanged(): bool
    {
        return $this->storage !== $this->origin;
    }

    /**
     * count
     *
     * @return  int
     */
    public function count()
    {
    }

    /**
     * jsonSerialize
     *
     * @return  mixed
     */
    public function jsonSerialize()
    {
    }
}
