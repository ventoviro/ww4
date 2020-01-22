<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Utilities\StrNormalise;

/**
 * The AbstractDriver class.
 */
abstract class AbstractDriver
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $platformName = '';

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * AbstractPlatform constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    /**
     * Connect to Database.
     *
     * @return  mixed
     */
    abstract public function connect();

    /**
     * Discount the database.
     *
     * @return  mixed
     */
    abstract public function disconnect();

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        return $this->platformName;
    }

    public function getPlatform(): AbstractPlatform
    {
        if (!$this->platform) {
            $this->platform = AbstractPlatform::create($this->platformName, $this->db);
        }

        return $this->platform;
    }

    /**
     * @param  string  $platformName
     *
     * @return  static  Return self to support chaining.
     */
    public function setPlatformName(string $platformName)
    {
        $this->platformName = $platformName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param  mixed  $connection
     *
     * @return  static  Return self to support chaining.
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
