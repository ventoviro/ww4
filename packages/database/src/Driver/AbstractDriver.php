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

    public static function create(string $driver, DatabaseAdapter $db)
    {
        $class = __NAMESPACE__ . '\\' . StrNormalise::toPascalCase($driver) . 'Driver';

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
}
