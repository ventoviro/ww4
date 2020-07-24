<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Database\Schema\Column\Column;
use Windwalker\Query\Grammar\AbstractGrammar;

/**
 * The AbstractSchemaManager class.
 */
abstract class AbstractSchemaManager
{
    protected $platform = '';

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * AbstractSchema constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    public static function create(string $platform, DatabaseAdapter $db)
    {
        $class = __NAMESPACE__ . '\\' . AbstractPlatform::getPlatformName($platform) . 'SchemaManager';

        return new $class($db);
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        return $this->platform;
    }

    public function getPlatform(): AbstractPlatform
    {
        return $this->db->getPlatform();
    }

    /**
     * getGrammar
     *
     * @return  AbstractGrammar
     */
    public function getGrammar(): AbstractGrammar
    {
        return $this->getPlatform()->getGrammar();
    }
}
