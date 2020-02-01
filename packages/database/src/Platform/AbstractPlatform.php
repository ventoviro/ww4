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
abstract class AbstractPlatform implements PlatformInterface
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
     * @var Grammar
     */
    protected $grammar;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    public static function create(string $platform, DatabaseAdapter $db)
    {
        switch (strtolower($platform)) {
            case 'pgsql':
            case 'postgresql':
                $platform = 'PostgreSQL';
                break;

            case 'sqlsrv':
            case 'sqlserver':
                $platform = 'SQLServer';
                break;

            case 'mysql':
                $platform = 'MySQL';
                break;

            case 'sqlite':
                $platform = 'SQLite';
                break;
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
        if (!$this->grammar) {
            $this->grammar = $this->createQuery()->getGrammar();
        }

        return $this->grammar;
    }

    public function createQuery(): Query
    {
        return new Query($this->db->getDriver(), $this->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
