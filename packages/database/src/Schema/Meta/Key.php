<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

/**
 * The Index class.
 */
class Key
{
    /**
     * @var string
     */
    const TYPE_UNIQUE = 'unique index';

    /**
     * @var string
     */
    const TYPE_INDEX = 'index';

    /**
     * @var string
     */
    const TYPE_PRIMARY = 'primary key';

    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = null;

    /**
     * Property type.
     *
     * @var  integer
     */
    protected $type = null;

    /**
     * Property columns.
     *
     * @var  array
     */
    protected $columns = [];

    /**
     * Property comment.
     *
     * @var  string
     */
    protected $comment = '';

    /**
     * Key constructor.
     *
     * @param int    $type
     * @param array  $columns
     * @param string $name
     * @param string $comment
     */
    public function __construct($type = null, $columns = [], $name = null, $comment = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->columns = (array) $columns;
        $this->comment = $comment;
    }

    /**
     * Method to get property Name
     *
     * @return  string
     */
    public function getName()
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
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to get property Type
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Method to set property type
     *
     * @param   int $type
     *
     * @return  static  Return self to support chaining.
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to get property Columns
     *
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Method to set property columns
     *
     * @param   array $columns
     *
     * @return  static  Return self to support chaining.
     */
    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Method to get property Comment
     *
     * @return  string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Method to set property comment
     *
     * @param   string $comment
     *
     * @return  static  Return self to support chaining.
     */
    public function comment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
