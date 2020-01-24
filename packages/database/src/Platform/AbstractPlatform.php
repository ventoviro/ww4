<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Query\Query;

/**
 * The AbstractPlatform class.
 */
abstract class AbstractPlatform
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    public static function create(string $platform, DatabaseAdapter $db)
    {
        if ($platform === 'pgsql') {
            $platform = 'postgresql';
        }

        $class = __NAMESPACE__ . '\\' . ucfirst($platform) . 'Platform';

        return new $class($db);
    }

    /**
     * AbstractPlatform constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    public function getGrammar(): Grammar
    {
        return $this->getQuery()->getGrammar();
    }

    public function getQuery(): Query
    {
        // if (!$this->query) {
        //     $this->query = new Query($this->db->getDriver(), $this->name);
        // }

        return new Query($this->db->getDriver(), $this->name);
    }
}
