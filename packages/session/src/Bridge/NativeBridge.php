<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Bridge;

use Windwalker\Data\Format\FormatInterface;
use Windwalker\Data\Format\JsonFormat;
use Windwalker\Session\Handler\HandlerInterface;
use Windwalker\Session\Handler\NativeHandler;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The ArrayBridge class.
 */
class NativeBridge implements BridgeInterface
{
    use OptionAccessTrait;

    protected HandlerInterface $handler;

    protected ?string $id = null;

    protected int $status = PHP_SESSION_NONE;

    protected array $storage = [];

    protected ?string $origin = null;

    /**
     * @var FormatInterface|null
     */
    protected ?FormatInterface $format;

    /**
     * NativeBridge constructor.
     *
     * @see https://gist.github.com/franksacco/d6e943c41189f8ee306c182bf8f07654
     *
     * @param  HandlerInterface|null  $handler
     * @param  FormatInterface|null   $format
     */
    public function __construct(?HandlerInterface $handler = null, ?FormatInterface $format = null)
    {
        $this->handler = $handler ?? new NativeHandler();
        $this->format = $format ?? new JsonFormat();
    }

    /**
     * start
     *
     * @return  bool
     */
    public function start(): bool
    {
        $this->handler->open($this->getOption('save_path'), $this->getOption('session_name'));

        register_shutdown_function([$this, 'writeClose']);

        $id = $this->getId();

        if ($id === null || (ini_get('session.use_strict_mode') && !$this->handler->validateId($id))) {
            $this->setId($id = $this->createId());
        }

        $this->origin = $dataString = $this->handler->read($id) ?? '';

        $_SESSION = (array) $this->format->parse($dataString);

        $this->status = PHP_SESSION_ACTIVE;

        return true;
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
        $this->origin = $data = $this->format->dump($_SESSION);

        if ($deleteOld) {
            $this->handler->destroy($this->getId());
        } else {
            $this->handler->write($this->getId(), $data);
        }

        $this->handler->close();
        $this->handler->open($this->getOption('save_path'), $this->getOption('session_name'));

        $this->setId($this->createId());

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
        if (true) {
            $this->handler->gc($this->getOption('gc_maxlifetime'));
        }

        $data = $this->format->dump($_SESSION);

        if (
            ini_get('session.lazy_write')
            && $this->origin === $data
            && $this->handler instanceof \SessionUpdateTimestampHandlerInterface
        ) {
            $r = $this->handler->updateTimestamp($this->getId(), $data);
        } else {
            $r = $this->handler->write($this->getId(), $data);
        }

        if ($unset) {
            $_SESSION = [];
        }

        $this->status = PHP_SESSION_DISABLED;

        return $r;
    }

    /**
     * destroy
     *
     * @return  void
     */
    public function destroy(): void
    {
        $this->handler->destroy($this->getId());

        $this->handler->close();
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

    /**
     * generateId
     *
     * @return  string
     *
     * @throws \Exception
     */
    protected function createId(): string
    {
        return session_create_id();
    }
}
