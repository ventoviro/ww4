<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Queue\Driver;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Query\Query;
use Windwalker\Queue\QueueMessage;

/**
 * The DatabaseQueueDriver class.
 *
 * @since  3.2
 */
class DatabaseQueueDriver implements QueueDriverInterface
{
    /**
     * Property db.
     *
     * @var  DatabaseAdapter
     */
    protected DatabaseAdapter $db;

    protected string $table;

    protected string $queue;

    protected int $timeout;

    /**
     * DatabaseQueueDriver constructor.
     *
     * @param DatabaseAdapter $db
     * @param string                 $queue
     * @param string                 $table
     * @param int                    $timeout
     */
    public function __construct(DatabaseAdapter $db, string $queue = 'default', string $table = 'queue_jobs', int $timeout = 60)
    {
        $this->db = $db;
        $this->table = $table;
        $this->queue = $queue;
        $this->timeout = $timeout;
    }

    /**
     * push
     *
     * @param  QueueMessage  $message
     *
     * @return int|string
     * @throws \Exception
     */
    public function push(QueueMessage $message): int|string
    {
        $time = new \DateTimeImmutable('now');

        $data = [
            'queue' => $message->getQueueName() ?: $this->queue,
            'body' => json_encode($message, JSON_THROW_ON_ERROR),
            'attempts' => 0,
            'created' => $time->format('Y-m-d H:i:s'),
            'visibility' => $time->modify(sprintf('+%dseconds', $message->getDelay())),
            'reserved' => null,
        ];

        $data = $this->db->getWriter()->insertOne($this->table, $data, 'id');

        return $data['id'];
    }

    /**
     * pop
     *
     * @param  string|null  $queue
     *
     * @return QueueMessage|null
     * @throws \Throwable
     */
    public function pop(?string $queue = null): ?QueueMessage
    {
        $queue = $queue ?: $this->queue;

        $now = new \DateTimeImmutable('now');

        $query = $this->db->getQuery(true);

        $query->select('*')
            ->from($this->table)
            ->where('queue', $queue)
            ->where('visibility', '<=', $now)
            ->orWhere(
                function (Query $query) use ($now) {
                    $query->where('reserved', null)
                        ->where('reserved', '<', $now->modify('-' . $this->timeout . 'seconds'));
                }
            )
            ->forUpdate();

        $data = $this->db->transaction(function () use ($now, $query) {
            $data = $this->db->prepare($query)->get();

            if (!$data) {
                return null;
            }

            $data['attempts']++;

            $values = ['reserved' => $now, 'attempts' => $data['attempts']];

            $this->db->getWriter()->updateBatch($this->table, $values, ['id' => $data['id']]);

            return $data;
        });

        if ($data === null) {
            return null;
        }

        $message = new QueueMessage();

        $message->setId($data['id']);
        $message->setAttempts($data['attempts']);
        $message->setBody(json_decode($data['body'], true, 512, JSON_THROW_ON_ERROR));
        $message->setRawBody($data['body']);
        $message->setQueueName($queue);

        return $message;
    }

    /**
     * delete
     *
     * @param  QueueMessage  $message
     *
     * @return static
     */
    public function delete(QueueMessage $message)
    {
        $queue = $message->getQueueName() ?: $this->queue;

        $this->db->delete($this->table)
            ->where('id', $message->getId())
            ->where('queue', $queue)
            ->execute();

        return $this;
    }

    /**
     * release
     *
     * @param QueueMessage|string $message
     *
     * @return static
     * @throws \Exception
     */
    public function release(QueueMessage $message)
    {
        $queue = $message->getQueueName() ?: $this->queue;

        $time = new \DateTimeImmutable('now');
        $time = $time->modify('+' . $message->getDelay() . 'seconds');

        $values = [
            'reserved' => null,
            'visibility' => $time,
        ];

        $this->db->getWriter()->updateBatch(
            $this->table,
            $values,
            [
                'id' => $message->getId(),
                'queue' => $queue,
            ]
        );

        return $this;
    }

    /**
     * Method to get property Table
     *
     * @return  string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Method to set property table
     *
     * @param   mixed $table
     *
     * @return  static  Return self to support chaining.
     */
    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Method to get property Db
     *
     * @return  DatabaseAdapter
     */
    public function getDb(): DatabaseAdapter
    {
        return $this->db;
    }

    /**
     * Method to set property db
     *
     * @param   DatabaseAdapter $db
     *
     * @return  static  Return self to support chaining.
     */
    public function setDb(DatabaseAdapter $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Reconnect database to avoid long connect issues.
     *
     * @return  static
     */
    public function reconnect()
    {
        $this->disconnect();

        $this->db->connect();

        return $this;
    }

    /**
     * Disconnect DB.
     *
     * @return  static
     *
     * @since  3.5.2
     */
    public function disconnect()
    {
        $this->db->disconnect();

        return $this;
    }
}
