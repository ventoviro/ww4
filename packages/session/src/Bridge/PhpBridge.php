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
use Windwalker\Data\Format\PhpSerializeFormat;
use Windwalker\Session\Handler\HandlerInterface;
use Windwalker\Session\Handler\NativeHandler;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The ArrayBridge class.
 */
class PhpBridge implements BridgeInterface
{
    use OptionAccessTrait;

    protected ?string $id = null;

    protected ?string $name = null;

    protected int $status = PHP_SESSION_NONE;

    protected array $storage = [];

    protected ?string $origin = null;

    protected ?HandlerInterface $handler = null;

    protected ?FormatInterface $format = null;

    /**
     * NativeBridge constructor.
     *
     * @see https://gist.github.com/franksacco/d6e943c41189f8ee306c182bf8f07654
     *
     * @param  array                  $options
     * @param  HandlerInterface|null  $handler
     * @param  FormatInterface|null   $format
     */
    public function __construct(array $options = [], HandlerInterface $handler = null, ?FormatInterface $format = null)
    {
        $this->handler = $handler ?? new NativeHandler();
        $this->format = $format ?? new PhpSerializeFormat();

        $this->prepareOptions(
            [
                'auto_commit' => false
            ],
            $options
        );
    }

    /**
     * start
     *
     * @return  bool
     */
    public function start(): bool
    {
        $this->handler->open($this->getOptionAndINI('save_path'), $this->getSessionName());

        if ($this->getOption('auto_commit')) {
            register_shutdown_function([$this, 'writeClose']);
        }

        $id = $this->getId();

        if (
            $id === null
            || (
                $this->getOptionAndINI('use_strict_mode')
                && $this->handler instanceof \SessionUpdateTimestampHandlerInterface
                && !$this->handler->validateId($id)
            )
        ) {
            $this->setId($id = $this->createId());
        }

        $this->origin = $dataString = $this->handler->read($id) ?: '';

        $this->storage = (array) ($this->format->parse($dataString) ?: []);

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
        return $this->name ??= session_name();
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
        $this->name = $name;
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
        $this->handler->open($this->getOptionAndINI('save_path'), $this->getSessionName());

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
        if ($this->status !== PHP_SESSION_ACTIVE) {
            return true;
        }

        if ($this->gcEnabled()) {
            show('GC');
            $this->handler->gc($this->getOptionAndINI('gc_maxlifetime') ?? 1440);
        }

        $data = $this->format->dump($this->storage);

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

    public function gcEnabled(): bool
    {
        $probability = (int) $this->getOptionAndINI('gc_probability');
        $divisor = (int) $this->getOptionAndINI('gc_divisor');

        if ($probability === 0 || $divisor === 0) {
            return false;
        }

        return random_int(1, $divisor) <= $probability;
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

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * unset
     *
     * @return  bool
     */
    public function unset(): bool
    {
        $this->storage = [];

        return true;
    }

    protected function getOptionAndINI(string $name)
    {
        return $this->getOption($name) ?? ini_get('session.' . $name);
    }
}
