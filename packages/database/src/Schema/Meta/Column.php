<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

use Windwalker\Database\Schema\DataType;
use Windwalker\Utilities\StrNormalise;
use Windwalker\Utilities\TypeCast;

/**
 * The Column class.
 */
class Column
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var int
     */
    protected $ordinalPosition = 1;

    /**
     * @var mixed
     */
    protected $columnDefault = null;

    /**
     * @var bool
     */
    protected $isNullable = false;

    /**
     * @var string
     */
    protected $dataType = 'text';

    /**
     * @var int
     */
    protected $characterMaximumLength;

    /**
     * @var int
     */
    protected $characterOctetLength;

    /**
     * @var int
     */
    protected $numericPrecision;

    /**
     * @var int
     */
    protected $numericScale;

    /**
     * @var bool
     */
    protected $numericUnsigned;

    /**
     * @var null|string
     */
    protected $comment = null;

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @var array
     */
    protected $erratas = [];

    /**
     * @var string|null
     */
    protected $after;

    /**
     * Column constructor.
     *
     * @param  string  $name
     * @param  string  $dataType
     * @param  bool    $isNullable
     * @param  mixed   $columnDefault
     * @param  array   $options
     */
    public function __construct(
        string $name = '',
        string $dataType = 'char',
        bool $isNullable = false,
        $columnDefault = null,
        array $options = []
    ) {
        $this->name          = $name;
        $this->columnDefault = $columnDefault;
        $this->isNullable    = $isNullable;
        $this->dataType($dataType);

        $this->bind($options);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     *
     * @return  static  Return self to support chaining.
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrdinalPosition(): ?int
    {
        return $this->ordinalPosition;
    }

    /**
     * @param  int  $ordinalPosition
     *
     * @return  static  Return self to support chaining.
     */
    public function ordinalPosition(?int $ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumnDefault()
    {
        return $this->columnDefault;
    }

    /**
     * @param  mixed  $value
     *
     * @return  static  Return self to support chaining.
     */
    public function columnDefault($value)
    {
        $this->columnDefault = $value;

        return $this;
    }

    /**
     * Alias of setColumnDefault().
     *
     * @param  mixed  $value
     *
     * @return  static  Return self to support chaining.
     */
    public function defaultValue($value)
    {
        return $this->columnDefault($value);
    }

    /**
     * @return bool
     */
    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * @param  bool  $isNullable
     *
     * @return  static  Return self to support chaining.
     */
    public function isNullable(bool $isNullable)
    {
        $this->isNullable = $isNullable;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @param  string  $dataType
     *
     * @return  static  Return self to support chaining.
     */
    public function dataType(string $dataType)
    {
        [$dataType, $precision, $scale] = DataType::extract($dataType);

        $this->dataType = $dataType;

        $this->setLengthByType(
            TypeCast::tryInteger($precision, true),
            TypeCast::tryInteger($scale, true)
        );

        return $this;
    }

    /**
     * length
     *
     * @param string|int $value
     *
     * @return  static
     */
    public function length($value)
    {
        [$dataType, $precision, $scale] = DataType::extract("{$this->dataType}($value)");

        $this->setLengthByType(
            TypeCast::tryInteger($precision, true),
            TypeCast::tryInteger($scale, true)
        );

        return $this;
    }

    private function setLengthByType(?int $precision, ?int $scale): void
    {
        if ($this->isNumeric()) {
            $this->precision($precision);
            $this->scale($scale);
            return;
        }

        $this->characterOctetLength((int) $precision);
    }

    public function getLength(): string
    {
        if ($this->characterOctetLength !== null) {
            return (string) $this->characterOctetLength;
        }

        return implode(',', array_filter([$this->numericPrecision, $this->numericScale]));
    }

    public function isNumeric(): bool
    {
        $type = $this->dataType;

        return in_array(
            strtolower($type),
            [
                'int',
                'integer',
                'tinyint',
                'tinyinteger',
                'bigint',
                'biginteger',
                'smallint',
                'smallinteger',
                'float',
                'double',
                'real',
                'decimal',
                'numeric'
            ],
            true
        );
    }

    /**
     * @return int
     */
    public function getCharacterMaximumLength(): ?int
    {
        return $this->characterMaximumLength;
    }

    /**
     * @param  int  $characterMaximumLength
     *
     * @return  static  Return self to support chaining.
     */
    public function characterMaximumLength(?int $characterMaximumLength)
    {
        $this->characterMaximumLength = $characterMaximumLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getCharacterOctetLength(): ?int
    {
        return $this->characterOctetLength;
    }

    /**
     * @param  int  $characterOctetLength
     *
     * @return  static  Return self to support chaining.
     */
    public function characterOctetLength(?int $characterOctetLength)
    {
        $this->characterOctetLength = $characterOctetLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumericPrecision(): ?int
    {
        return $this->numericPrecision;
    }

    /**
     * @param  int  $precision
     *
     * @return  static  Return self to support chaining.
     */
    public function precision(?int $precision)
    {
        $this->numericPrecision = $precision;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumericScale(): ?int
    {
        return $this->numericScale;
    }

    /**
     * @param  int  $scale
     *
     * @return  static  Return self to support chaining.
     */
    public function scale(?int $scale)
    {
        $this->numericScale = $scale;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNumericUnsigned(): bool
    {
        return $this->numericUnsigned;
    }

    /**
     * @param  bool  $unsigned
     *
     * @return  static  Return self to support chaining.
     */
    public function unsigned(bool $unsigned = true)
    {
        $this->numericUnsigned = $unsigned;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param  string|null  $comment
     *
     * @return  static  Return self to support chaining.
     */
    public function comment(?string $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @param  bool  $autoIncrement
     *
     * @return  static  Return self to support chaining.
     */
    public function autoIncrement(bool $autoIncrement = true)
    {
        $this->autoIncrement = $autoIncrement;

        return $this;
    }

    /**
     * @return array
     */
    public function getErratas(): array
    {
        return $this->erratas;
    }

    /**
     * @param  array  $erratas
     *
     * @return  static  Return self to support chaining.
     */
    public function erratas(array $erratas)
    {
        $this->erratas = $erratas;

        return $this;
    }
    /**
     * @return string|null
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }

    /**
     * @param  string|null  $after
     *
     * @return  static  Return self to support chaining.
     */
    public function after(?string $after)
    {
        $this->after = $after;

        return $this;
    }

    /**
     * bind
     *
     * @param  array  $data
     *
     * @return  static
     */
    public function bind(array $data)
    {
        foreach ($data as $key => $datum) {
            $prop = StrNormalise::toCamelCase($key);

            if (method_exists($this, $prop)) {
                $this->$prop($datum);
            } elseif (property_exists($this, $prop)) {
                $this->$prop = $datum;
            }
        }

        return $this;
    }

    /**
     * wrap
     *
     * @param array|static $data
     *
     * @return  static
     */
    public static function wrap($data)
    {
        if ($data instanceof static) {
            return $data;
        }

        return (new static())->bind($data);
    }

    /**
     * wrapList
     *
     * @param  array  $items
     *
     * @return  static[]
     */
    public static function wrapList(array $items): array
    {
        foreach ($items as $name => $item) {
            $items[$name] = static::wrap($item);
        }

        return $items;
    }
}
