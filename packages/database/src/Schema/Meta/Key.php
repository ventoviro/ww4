<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The Index class.
 */
class Key
{
    use OptionAccessTrait;

    /**
     * @var string
     */
    public const TYPE_INDEX = 'index';

    /**
     * @var string
     */
    public const TYPE_PRIMARY = 'primary key';

    /**
     * Property name.
     *
     * @var  string
     */
    protected $name;

    /**
     * Property type.
     *
     * @var  string
     */
    protected $type;

    /**
     * Property columns.
     *
     * @var  array
     */
    protected $columns = [];

    /**
     * Key constructor.
     *
     * @param  string  $type
     * @param  array   $columns
     * @param  string  $name
     * @param  array   $options
     */
    public function __construct(?string $type = null, array $columns = [], ?string $name = null, array $options = [])
    {
        $this->columns($columns)
            ->name($name)
            ->type($type);

        $this->prepareOptions([], $options);
    }

    /**
     * Method to get property Name
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Method to set property name
     *
     * @param   string $name
     *
     * @return  static  Return self to support chaining.
     */
    public function name(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to get property Type
     *
     * @return  string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Method to set property type
     *
     * @param   string $type
     *
     * @return  static  Return self to support chaining.
     */
    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to get property Columns
     *
     * @return  array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Method to set property columns
     *
     * @param   array|string $columns
     *
     * @return  static  Return self to support chaining.
     */
    public function columns($columns)
    {
        $this->columns = (array) $columns;

        return $this;
    }
}
