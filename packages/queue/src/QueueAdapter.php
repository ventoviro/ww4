<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Queue;

use Windwalker\Queue\Driver\QueueDriverInterface;
use Windwalker\Queue\Job\CallableJob;
use Windwalker\Queue\Job\JobInterface;

/**
 * The Queue class.
 *
 * @since  3.2
 */
class QueueAdapter
{
    /**
     * Property driver.
     *
     * @var QueueDriverInterface
     */
    protected QueueDriverInterface $driver;

    /**
     * QueueManager constructor.
     *
     * @param QueueDriverInterface $driver
     */
    public function __construct(QueueDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * push
     *
     * @param  mixed        $job
     * @param  int          $delay
     * @param  string|null  $queue
     * @param  array        $options
     *
     * @return int|string
     */
    public function push($job, int $delay = 0, ?string $queue = null, array $options = []): int|string
    {
        $message = $this->getMessageByJob($job);
        $message->setDelay($delay);
        $message->setQueueName($queue);
        $message->setOptions($options);

        return $this->driver->push($message);
    }

    /**
     * pushRaw
     *
     * @param  string|array  $body
     * @param  int           $delay
     * @param  string|null   $queue
     * @param  array         $options
     *
     * @return  int|string
     * @throws \JsonException
     */
    public function pushRaw(string|array $body, int $delay = 0, ?string $queue = null, array $options = []): int|string
    {
        if (is_string($body)) {
            json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        }

        $message = new QueueMessage();
        $message->setBody($body);
        $message->setDelay($delay);
        $message->setQueueName($queue);
        $message->setOptions($options);

        return $this->driver->push($message);
    }

    /**
     * pop
     *
     * @param  string|null  $queue
     *
     * @return QueueMessage|null
     */
    public function pop(?string $queue = null): ?QueueMessage
    {
        return $this->driver->pop($queue);
    }

    /**
     * delete
     *
     * @param QueueMessage|mixed $message
     *
     * @return  void
     */
    public function delete($message): void
    {
        if (!$message instanceof QueueMessage) {
            $msg = new QueueMessage();
            $msg->setId($message);

            $message = $msg;
        }

        $this->driver->delete($message);

        $message->setDeleted(true);
    }

    /**
     * release
     *
     * @param QueueMessage|mixed $message
     * @param int                $delay
     *
     * @return  void
     */
    public function release($message, int $delay = 0): void
    {
        if (!$message instanceof QueueMessage) {
            $msg = new QueueMessage();
            $msg->setId($message);
        }

        $message->setDelay($delay);

        $this->driver->release($message);
    }

    /**
     * getMessage
     *
     * @param mixed $job
     * @param array $data
     *
     * @return QueueMessage
     * @throws \InvalidArgumentException
     */
    public function getMessageByJob($job, array $data = []): QueueMessage
    {
        $message = new QueueMessage();

        $job = $this->createJobInstance($job);

        $data['class'] = get_class($job);

        $message->setName($job->getName());
        $message->setSerializedJob(serialize($job));
        $message->setData($data);

        return $message;
    }

    /**
     * createJobInstance
     *
     * @param mixed $job
     *
     * @return  JobInterface
     * @throws \InvalidArgumentException
     */
    protected function createJobInstance($job): JobInterface
    {
        if ($job instanceof JobInterface) {
            return $job;
        }

        // Create callable
        if (is_callable($job)) {
            $job = new CallableJob($job, md5(uniqid('', true)));
        }

        // Create by class name.
        if (is_string($job)) {
            if (!class_exists($job) || is_subclass_of($job, JobInterface::class)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Job should be a class which implements %s, %s given',
                        JobInterface::class,
                        $job
                    )
                );
            }

            $job = $this->createJobByClassName($job);
        }

        if (is_array($job)) {
            throw new \InvalidArgumentException('Job should not be array.');
        }

        return $job;
    }

    /**
     * Method to get property Driver
     *
     * @return  QueueDriverInterface
     */
    public function getDriver(): QueueDriverInterface
    {
        return $this->driver;
    }

    /**
     * Method to set property driver
     *
     * @param   QueueDriverInterface $driver
     *
     * @return  static  Return self to support chaining.
     */
    public function setDriver(QueueDriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * createJob
     *
     * @param  string  $job
     *
     * @return  JobInterface
     */
    protected function createJobByClassName(string $job): JobInterface
    {
        $job = new $job();

        return $job;
    }
}
