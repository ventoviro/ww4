<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Data\Traits\CollectionTrait;
use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Arr;
use function Windwalker\tap;

/**
 * The ArraySortConcern class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ArraySortTrait
{
    /**
     * Sort Dataset by key.
     *
     * @param  integer  $flags  You may modify the behavior of the sort using the optional parameter flags.
     *
     * @return  static  Support chaining.
     *
     * @since   3.5.2
     */
    public function ksort($flags = null)
    {
        return tap(clone $this, function ($new) use ($flags) {
            ksort($new->storage, $flags);
        });
    }

    /**
     * Sort DataSet by key in reverse order
     *
     * @param  integer  $flags  You may modify the behavior of the sort using the optional parameter flags.
     *
     * @return  static  Support chaining.
     *
     * @since   3.5.2
     */
    public function krsort($flags = null)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($flags) {
            krsort($new->storage, $flags);
        });
    }

    /**
     * Sort data.
     *
     * @param  integer  $flags  You may modify the behavior of the sort using the optional parameter flags.
     *
     * @return  static  Support chaining.
     *
     * @since   3.0
     */
    public function sort($flags = null)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($flags) {
            sort($new->storage, $flags);
        });
    }

    /**
     * Sort Data in reverse order.
     *
     * @param  integer  $flags  You may modify the behavior of the sort using the optional parameter flags.
     *
     * @return  static  Support chaining.
     *
     * @since   3.0
     */
    public function rsort($flags = null)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($flags) {
            rsort($new->storage, $flags);
        });
    }

    /**
     * Sort an array using a case insensitive "natural order" algorithm
     *
     * @return static
     */
    public function natcasesort()
    {
        return tap(clone $this, static function (ArrayObject $new) {
            natcasesort($new->storage);
        });
    }

    /**
     * Sort entries using a "natural order" algorithm
     *
     * @return static
     */
    public function natsort()
    {
        return tap(clone $this, static function (ArrayObject $new) {
            natsort($new->storage);
        });
    }

    /**
     * Sort the entries by value
     *
     * @param  int  $flags
     *
     * @return static
     */
    public function asort($flags = null)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($flags) {
            asort($new->storage, $flags);
        });
    }

    /**
     * Sort the entries with a user-defined comparison function and maintain key association
     *
     * @param  callable  $function
     *
     * @return static
     */
    public function uasort($function)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($function) {
            uasort($new->storage, $function);
        });
    }

    /**
     * Sort DataSet by keys using a user-defined comparison function
     *
     * @param  callable  $callable  The compare function used for the sort.
     *
     * @return  static  Support chaining.
     *
     * @since   3.5.2
     */
    public function uksort($callable)
    {
        return tap(clone $this, static function (ArrayObject $new) use ($callable) {
            uksort($new->storage, $callable);
        });
    }

    /**
     * sortColumn
     *
     * @param string $column
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function sortColumn(string $column)
    {
        $array = $this->dump();

        usort($array, static function ($a, $b) use ($column) {
            $aValue = Arr::get($a, $column);
            $bValue = Arr::get($b, $column);

            if (is_stringable($aValue) && is_stringable($bValue)) {
                return strcmp(
                    (string) $aValue,
                    (string) $bValue
                );
            }

            if ($aValue > $bValue) {
                return 1;
            }

            if ($bValue > $aValue) {
                return -1;
            }

            return 0;
        });

        return static::newInstance($array);
    }
}
