<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform\Concern;

/**
 * The PlatformMetaTrait class.
 */
trait PlatformMetaTrait
{
    /**
     * @var string|null
     */
    protected static $defaultSchema = null;

    /**
     * @var string
     */
    protected $name = '';

    public static function getPlatformName(string $platform): string
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

        return $platform;
    }

    public static function getShortName(string $platform): string
    {
        switch (strtolower($platform)) {
            case 'postgresql':
                $platform = 'pgsql';
                break;

            case 'sqlserver':
                $platform = 'sqlsrv';
                break;
        }

        return strtolower($platform);
    }

    /**
     * @return string
     */
    public static function getDefaultSchema(): ?string
    {
        return self::$defaultSchema;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
