<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Utilities\Classes\FlowControlTrait;
use Windwalker\Utilities\Classes\MarcoableTrait;

/**
 * The Query class.
 */
class Query implements QueryInterface
{
    use MarcoableTrait;
    use FlowControlTrait;

    /**
     * @var \WeakReference
     */
    protected $connection;



    /**
     * Query constructor.
     *
     * @param  mixed|\PDO  $connection
     */
    public function __construct($connection)
    {
        if (!$connection instanceof \WeakReference) {
            $connection = new \WeakReference($connection);
        }

        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return '';
    }

    /**
     * Method to get property Connection
     *
     * @return  \PDO|mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getConnection()
    {
        return $this->connection->get();
    }

    /**
     * Method to set property connection
     *
     * @param  \PDO|mixed  $connection
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setConnection($connection)
    {
        if (!$connection instanceof \WeakReference) {
            $connection = new \WeakReference($connection);
        }

        $this->connection = $connection;

        return $this;
    }
}
