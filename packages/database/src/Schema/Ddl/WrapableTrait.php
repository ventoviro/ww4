<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Ddl;

use Windwalker\Utilities\Arr;
use Windwalker\Utilities\StrNormalise;

/**
 * Trait WrapableTrait
 */
trait WrapableTrait
{
    /**
     * bind
     *
     * @param  array  $data
     *
     * @return  static
     */
    public function bind(array $data): static
    {
        foreach ($data as $key => $datum) {
            $prop = StrNormalise::toCamelCase($key);

            if (method_exists($this, $prop)) {
                $this->$prop($datum);
            } elseif (property_exists($this, $prop)) {
                $this->$prop = $datum;
            } else {
                $this->setOption($prop, $datum);
            }
        }

        return $this;
    }

    /**
     * wrap
     *
     * @param  array|static  $data
     *
     * @return  static
     */
    public static function wrap($data): static
    {
        if ($data instanceof static) {
            return $data;
        }

        return (new static())->bind($data);
    }

    /**
     * wrapList
     *
     * @param  array        $items
     * @param  string|null  $keyName
     *
     * @return  static[]
     */
    public static function wrapList(array $items, ?string $keyName = null): array
    {
        $newItems = [];

        foreach ($items as $key => $item) {
            $key = $keyName ? Arr::get($item, $keyName) : $key;

            $newItems[$key] = static::wrap($item);
        }

        return $newItems;
    }
}
