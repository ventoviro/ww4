<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Bridge;

use Windwalker\Session\Handler\HandlerInterface;

/**
 * The ArrayBridge class.
 */
class NativeBridge implements BridgeInterface
{
    protected HandlerInterface $handler;

    protected ?string $id = null;

    protected int $status = PHP_SESSION_NONE;

    protected array $storage = [];

    /**
     * NativeBridge constructor.
     *
     * @param  HandlerInterface  $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * start
     *
     * @return  bool
     */
    public function start(): bool
    {
        $dataString = $this->handler->read($this->getId());

        $r = session_decode($dataString);

        $this->status = PHP_SESSION_ACTIVE;

        return $r;
    }

    /**
     * isStarted
     *
     * @return  bool
     */
    public function isStarted(): bool
    {
        return $this->status === PHP_SESSION_ACTIVE;
    }

    /**
     * getId
     *
     * @return  string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * setId
     *
     * @param  string  $id
     *
     * @return  void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * getSessionName
     *
     * @return  string|null
     */
    public function getSessionName(): ?string
    {
    }

    /**
     * setSessionName
     *
     * @param  string  $name
     *
     * @return  void
     */
    public function setSessionName(string $name): void
    {
    }

    /**
     * regenerate
     *
     * @param  bool  $deleteOld
     *
     * @return  bool
     * @throws \Exception
     */
    public function regenerate(bool $deleteOld = false): bool
    {
        $data = session_encode();

        if ($deleteOld) {
            $this->handler->destroy($this->getId());
        }

        $this->setId(md5(random_bytes(32)));

        $this->handler->write($this->getId(), $data);

        return true;
    }

    /**
     * writeClose
     *
     * @param  bool  $unset
     *
     * @return  bool
     */
    public function writeClose(bool $unset = true): bool
    {
        $data = session_encode();

        $this->handler->write($this->getId(), $data);

        if ($unset) {
            $_SESSION = [];
        }
    }

    /**
     * destroy
     *
     * @return  void
     */
    public function destroy(): void
    {
    }

    /**
     * getStorage
     *
     * @return  array|null
     */
    public function &getStorage(): ?array
    {
        return $this->storage;
    }
}
