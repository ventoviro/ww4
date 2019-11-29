<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Wrapper;

use Windwalker\Utilities\Arr;

/**
 * The ValueReference class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ValueReference implements WrapperInterface
{
    /**
     * Property path.
     *
     * @var  string
     */
    public $path;

    /**
     * Property separator.
     *
     * @var  string|null
     */
    public $delimiter;

    /**
     * ValueReference constructor.
     *
     * @param  string  $path
     * @param  string  $delimiter
     */
    public function __construct(string $path, ?string $delimiter = null)
    {
        $this->path      = $path;
        $this->delimiter = $delimiter;
    }

    /**
     * Get wrapped value.
     *
     * @param  array|object  $src
     * @param  mixed         $default
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __invoke($src, $default = null)
    {
        return Arr::get($src, $this->path, (string) $this->delimiter);
    }
}
