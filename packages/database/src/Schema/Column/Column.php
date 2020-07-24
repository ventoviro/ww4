<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Column;

use Windwalker\Database\Manager\TableManager;
use Windwalker\Database\Schema\DataType;
use Windwalker\Query\Grammar\MySQLGrammar;
use Windwalker\Query\Query;
use Windwalker\Utilities\Classes\OptionAccessTrait;
use Windwalker\Utilities\StrNormalise;
use Windwalker\Utilities\TypeCast;

/**
 * The Column class.
 */
class Column
{
    use OptionAccessTrait;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var int
     */
    protected int $ordinalPosition = 1;

    /**
     * @var mixed
     */
    protected mixed $columnDefault = null;

    /**
     * @var bool
     */
    protected bool $isNullable = false;

    /**
     * @var string
     */
    protected string $dataType = 'text';

    /**
     * @var int
     */
    protected ?int $characterMaximumLength = null;

    /**
     * @var int
     */
    protected ?int $characterOctetLength = null;

    /**
     * @var int
     */
    protected ?int $numericPrecision = null;

    /**
     * @var int
     */
    protected ?int $numericScale = null;

    /**
     * @var bool
     */
    protected bool $numericUnsigned = false;

    /**
     * @var null|string
     */
    protected ?string $comment = null;

    /**
     * @var bool
     */
    protected bool $autoIncrement = false;

    /**
     * @var array
     */
    protected array $erratas = [];

    /**
     * @var string|null
     */
    protected ?string $after = null;

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
        mixed $columnDefault = null,
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
    public function name(string $name): static
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
     * @param  int|null  $ordinalPosition
     *
     * @return  static  Return self to support chaining.
     */
    public function ordinalPosition(?int $ordinalPosition): static
    {
        $this->ordinalPosition = $ordinalPosition;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumnDefault(): mixed
    {
        return $this->columnDefault;
    }

    /**
     * @param  mixed  $value
     *
     * @return  static  Return self to support chaining.
     */
    public function columnDefault($value): static
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
    public function defaultValue($value): static
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
    public function isNullable(bool $isNullable): static
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
    public function dataType(string $dataType): static
    {
        [$dataType, $precision, $scale] = DataType::extract($dataType);

        $this->dataType = $dataType;

        $this->setLengthByType(
            TypeCast::tryInteger($precision, true),
            TypeCast::tryInteger($scale, true)
        );

        return $this;
    }

    public function position(string $delta, string $pos): static
    {
        $this->setOption('position', [$delta, $pos]);

        return $this;
    }

    public function before(string $column): static
    {
        return $this->position('BEFORE', $column);
    }

    public function after(string $column): static
    {
        return $this->position('AFTER', $column);
    }

    /**
     * length
     *
     * @param  string|int  $value
     *
     * @return  static
     */
    public function length(string|int $value): static
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

    public function getLengthExpression(): ?string
    {
        if ($this->characterOctetLength !== null) {
            return (string) $this->characterOctetLength;
        }

        if ($this->numericPrecision !== null || $this->numericScale !== null) {
            return implode(',', array_filter([$this->numericPrecision, $this->numericScale]));
        }

        return null;
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
                'numeric',
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
    public function characterMaximumLength(?int $characterMaximumLength): static
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
    public function characterOctetLength(?int $characterOctetLength): static
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
    public function precision(?int $precision): static
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
    public function scale(?int $scale): static
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
    public function unsigned(bool $unsigned = true): static
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
    public function comment(?string $comment = null): static
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
    public function autoIncrement(bool $autoIncrement = true): static
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
    public function erratas(array $erratas): static
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
    public function after(?string $after): static
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
    public function bind(array $data): static
    {
        foreach ($data as $key => $datum) {
            $prop = StrNormalise::toCamelCase($key);

            if (method_exists($this, $prop)) {
                $this->$prop($datum);
            } elseif (property_exists($this, $prop)) {
                $this->$prop = $datum;
            } else {
                $this->setOption($prop, $datum);
            }
        }

        return $this;
    }

    /**
     * wrap
     *
     * @param  array|static  $data
     *
     * @return  static
     */
    public static function wrap($data): static
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

    public function getTypeExpression(TableManager $table): string
    {
        $expr = $table->getDb()->quoteName($this->name);

        $length = $this->getLengthExpression();

        if ($length !== null) {
            $expr .= '(' . $length . ')';
        }

        return $expr;
    }

    public function getCreateExpression(TableManager $table): string
    {
        $expr = $this->getTypeExpression($table);

        if (!$this->isNullable) {
            $expr .= ' NOT NULL';
        }

        $db = $table->getDb();

        if ($this->columnDefault !== null || $this->isNullable) {
            $expr .= ' DEFAULT ' . $db->quote($this->columnDefault);
        }

        if ($db->getPlatform()->getGrammar() instanceof MySQLGrammar) {
            if ($this->comment !== null) {
                $expr .= ' COMMENT ' . $db->quote($this->columnDefault);
            }

            if ($this->getOption('position') !== null) {
                [$delta, $pos] = $this->getOption('position');
                $expr .= ' POSITION ' . $delta . ' ' . $db->quoteName($pos);
            }
        }

        return $expr;
    }
}
