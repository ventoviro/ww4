<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Clause;

use Windwalker\Database\Schema\Ddl\Column;
use Windwalker\Database\Schema\Ddl\Constraint;
use Windwalker\Query\Query;

/**
 * The Alter class.
 */
class AlterClause implements \Stringable
{
    protected Clause $clause;

    /**
     * @var Query
     */
    protected Query $query;

    /**
     * Alter constructor.
     *
     * @param  Query  $query
     */
    public function __construct(Query $query)
    {
        $this->query  = $query;
        $this->clause = $query->clause('ALTER', [], ",\n");
    }

    public function target(string $target, string $targetName): static
    {
        $this->clause->setName(
            $this->query->format("ALTER %r %n\n", $target, $targetName)
        );

        return $this;
    }

    /**
     * addIndex
     *
     * @param  string    $name
     * @param  string[]  $columns
     *
     * @return  Clause
     */
    public function addIndex(string $name, array $columns = []): Clause
    {
        $this->clause->append(
            $clause = $this->query->clause('ADD INDEX')
                ->append($this->query->quoteName($name))
                ->append(
                    $this->query->clause(
                        '()',
                        $columns,
                        ','
                    )
                )
        );

        return $clause;
    }

    public function addConstraint(?string $name, string $type, array $columns = []): Clause
    {
        $this->clause->append(
            $clause = $this->query->clause('ADD CONSTRAINT')
                ->append($name ? $this->query->quoteName($name) : '')
                ->append(
                    $this->query->clause(
                        $type . ' ()',
                        $columns,
                        ",\n"
                    )
                )
        );

        return $clause;
    }

    public function addPrimaryKey(?string $name, array $columns): Clause
    {
        return $this->addConstraint(null, Constraint::TYPE_PRIMARY_KEY, $columns);
    }

    public function addUniqueKey(string $name, array $columns): Clause
    {
        return $this->addConstraint($name, Constraint::TYPE_UNIQUE, $columns);
    }

    public function addForeignKey(
        string $name,
        array $columns,
        array $refColumns,
        ?string $onUpdate,
        ?string $onDelete
    ): Clause {
        $clause = $this->addConstraint($name, Constraint::TYPE_FOREIGN_KEY, $columns)
            ->append(
                $this->query->clause(
                    'REFERENCES ()',
                    $refColumns,
                    ",\n"
                )
            );

        if ($onUpdate) {
            $clause->append(['ON UPDATE', $onUpdate]);
        }

        if ($onDelete) {
            $clause->append(['ON DELETE', $onDelete]);
        }

        return $clause;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->clause;
    }

    /**
     * @return Clause
     */
    public function getClause(): Clause
    {
        return $this->clause;
    }
}
