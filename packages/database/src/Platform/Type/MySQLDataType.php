<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform\Type;

/**
 * The MysqlType class.
 *
 * @since  2.0
 */
class MySQLDataType extends DataType
{
    public const INTEGER = 'int';

    public const BOOLEAN = 'bool';

    public const ENUM = 'enum';

    public const SET = 'set';

    /**
     * Property types.
     *
     * @var  array
     */
    public static array $defaultLengths = [
        self::INTEGER => 11,
        self::BIGINT => 20,
    ];

    /**
     * "Length", "Default", "PHP Type"
     *
     * @var  array
     */
    public static array $typeDefinitions = [
        self::BOOLEAN => [1, 0, 'bool'],
        self::INTEGER => [11, 0, 'int'],
        self::BIGINT => [20, 0, 'int'],
        self::ENUM => [null, '', 'string'],
        self::SET => [null, '', 'string'],
        self::DATETIME => [null, '1000-01-01 00:00:00', 'string'],
        self::TEXT => [null, false, 'string'],
    ];

    /**
     * Property typeMapping.
     *
     * @var  array
     */
    protected static array $typeMapping = [
        DataType::INTEGER => 'int',
        DataType::BIT => self::TINYINT,
        DataType::BOOLEAN => self::BOOLEAN,
    ];
}
