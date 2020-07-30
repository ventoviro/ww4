<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Manager;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Platform\AbstractPlatform;

/**
 * The AbstractDbManager class.
 */
abstract class AbstractMetaManager
{
    /**
     * @var string
     */
    protected ?string $name = null;

    /**
     * @var DatabaseAdapter
     */
    protected DatabaseAdapter $db;

    /**
     * AbstractDbManager constructor.
     *
     * @param  string|null      $name
     * @param  DatabaseAdapter  $db
     */
    public function __construct(?string $name, DatabaseAdapter $db)
    {
        $this->db = $db;
        $this->name = $name;
    }

    /**
     * @return DatabaseAdapter
     */
    public function getDb(): DatabaseAdapter
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPlatform(): AbstractPlatform
    {
        return $this->db->getPlatform();
    }

    /**
     * reset
     *
     * @return  static
     */
    abstract public function reset(): static;
}
