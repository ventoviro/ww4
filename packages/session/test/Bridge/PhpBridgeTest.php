<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Test\Bridge;

use PHPUnit\Framework\TestCase;
use Windwalker\Session\Bridge\PhpBridge;
use Windwalker\Session\Handler\FilesystemHandler;

/**
 * The PhpBridgeTest class.
 */
class PhpBridgeTest extends TestCase
{
    protected ?PhpBridge $instance;

    protected static string $sess1 = '93cd6b3ec9f36b23d68e9385942dc41c';
    protected static string $sess2 = 'fa0a731220e28af75afba7135723015e';

    public function getTempPath(): string
    {
        return __DIR__ . '/../../tmp';
    }

    /**
     * @see  PhpBridge::start
     */
    public function testStart(): void
    {
        $this->instance->start();

        self::assertEquals(
            PHP_SESSION_ACTIVE,
            $this->instance->getStatus()
        );

        $data = &$this->instance->getStorage();
        $data['flower'] = 'Sakura';


    }

    /**
     * @see  PhpBridge::getStatus
     */
    public function testGetStatus(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::gcEnabled
     */
    public function testGcEnabled(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::setSessionName
     */
    public function testSetSessionName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::getId
     */
    public function testGetId(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::unset
     */
    public function testUnset(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::setId
     */
    public function testSetId(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::isStarted
     */
    public function testIsStarted(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::getStorage
     */
    public function testGetStorage(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::writeClose
     */
    public function testWriteClose(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::destroy
     */
    public function testDestroy(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::getSessionName
     */
    public function testGetSessionName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PhpBridge::regenerate
     */
    public function testRegenerate(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new PhpBridge(
            [],
            new FilesystemHandler($this->getTempPath())
        );
    }

    protected function tearDown(): void
    {
        $this->instance->writeClose();
    }
}
