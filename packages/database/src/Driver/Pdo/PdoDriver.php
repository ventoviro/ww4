<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Query\Query;

/**
 * The PdoDriver class.
 */
class PdoDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected $name = 'pdo';

    /**
     * @var string
     */
    protected $platformName = 'odbc';

    /**
     * @inheritDoc
     */
    public function __construct(DatabaseAdapter $db)
    {
        parent::__construct($db);
    }

    protected function getConnectionClass(): string
    {
        $class = __NAMESPACE__ . '\Pdo%sConnection';

        return sprintf(
            $class,
            ucfirst($this->platformName)
        );
    }

    /**
     * @inheritDoc
     */
    public function prepare($query, array $options = []): StatementInterface
    {
        /** @var \PDO $pdo */
        $pdo = $this->getConnection()->get();

        $query = $this->handleQuery($query, $bounded);

        return new PdoStatement($pdo->prepare($query, $options), $bounded);
    }

    /**
     * @inheritDoc
     */
    public function execute($query): bool
    {
        return $this->prepare($query)->execute();
    }

    /**
     * @inheritDoc
     */
    public function quote(string $value): string
    {
    }

    /**
     * @inheritDoc
     */
    public function escape(string $value): string
    {
    }
}
