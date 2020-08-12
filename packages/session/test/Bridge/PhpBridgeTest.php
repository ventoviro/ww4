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
use Windwalker\Filesystem\Filesystem;
use Windwalker\Session\Bridge\PhpBridge;

/**
 * The PhpBridgeTest class.
 */
class PhpBridgeTest extends TestCase
{
    protected ?PhpBridge $instance;
    protected static string $sess1 = '93cd6b3ec9f36b23d68e9385942dc41c';
    protected static string $sess2 = 'fa0a731220e28af75afba7135723015e';

    /**
     * @runInSeparateProcess
     *
     * @see  PhpBridge::start
     */
    public function testStart(): void
    {
        self::resetSessions();

        session_id(static::$sess1);

        $this->instance->start();

        self::assertEquals(
            [
                'flower' => 'Sakura',
                'animal' => 'Cat'
            ],
            $_SESSION
        );

        $this->instance->writeClose();

        self::assertEquals(
            [],
            $_SESSION
        );

        session_id(static::$sess2);

        $this->instance->start();

        self::assertEquals(
            [
                'flower' => 'Rose',
                'tree' => 'Oak'
            ],
            $_SESSION
        );

        $_SESSION['animal'] = 'Bird';

        $this->instance->writeClose();

        self::assertEquals(
            'flower|s:4:"Rose";tree|s:3:"Oak";animal|s:4:"Bird";',
            file_get_contents(self::getSessionPath() . '/' . 'sess_' . static::$sess2),
        );
    }

    protected static function resetSessions(): void
    {
        session_save_path(static::getSessionPath());

        foreach (Filesystem::glob(self::getSessionPath() . '/sess_*') as $fileObject) {
            $fileObject->delete();
        }

        $buffer = 'flower|s:6:"Sakura";animal|s:3:"Cat";';

        file_put_contents(self::getSessionPath() . '/' . 'sess_' . static::$sess1, $buffer);

        $buffer = 'flower|s:4:"Rose";tree|s:3:"Oak";';

        file_put_contents(self::getSessionPath() . '/' . 'sess_' . static::$sess2, $buffer);
    }

    protected static function getSessionPath(): string
    {
        return __DIR__ . '/../../tmp';
    }

    /**
     * @see  PhpBridge::destroy
     *
     * @runInSeparateProcess
     */
    public function testDestroy(): void
    {
        self::resetSessions();

        $this->instance->setId(static::$sess1);
        $this->instance->start();

        self::assertFileExists(self::getSessionPath() . '/' . 'sess_' . static::$sess1);

        $this->instance->destroy();

        self::assertFileDoesNotExist(self::getSessionPath() . '/' . 'sess_' . static::$sess1);
        self::assertEquals([], $_SESSION);
    }

    /**
     * @see  PhpBridge::setSessionName
     *
     * @runInSeparateProcess
     */
    public function testStartSessionFromCookie(): void
    {
        static::resetSessions();

        $_COOKIE['PHPSESSID'] = 'windwalkersessions';

        $this->instance->start();

        $_SESSION['framework'] = 'Windwalker';

        $this->instance->writeClose();

        self::assertEquals(
            'framework|s:10:"Windwalker";',
            file_get_contents(self::getSessionPath() . '/sess_' . $_COOKIE['PHPSESSID'])
        );
    }

    /**
     * @see  PhpBridge::setSessionName
     * @runInSeparateProcess
     */
    public function testSetSessionName(): void
    {
        static::resetSessions();

        $name = 'WINDWALKER_SESSID';

        $_COOKIE[$name] = 'windwalkersessions';

        $this->instance->setSessionName($name);
        $this->instance->start();

        $_SESSION['framework'] = 'Windwalker';

        $this->instance->writeClose();

        self::assertEquals(
            'framework|s:10:"Windwalker";',
            file_get_contents(self::getSessionPath() . '/sess_' . $_COOKIE[$name])
        );

        self::assertEquals(
            $name,
            $this->instance->getSessionName()
        );
    }

    /**
     * @see  PhpBridge::isStarted
     * @runInSeparateProcess
     */
    public function testIsStartedAndRestart(): void
    {
        self::resetSessions();

        session_id(static::$sess1);

        self::assertFalse($this->instance->isStarted());

        $this->instance->start();

        self::assertTrue($this->instance->isStarted());

        $this->instance->restart(false);

        $newId = $this->instance->getId();

        $this->instance->writeClose();

        self::assertFileEquals(
            self::getSessionPath() . '/sess_' . static::$sess1,
            self::getSessionPath() . '/sess_' . $newId,
        );
    }

    /**
     * @see  PhpBridge::restart
     * @runInSeparateProcess
     */
    public function testRestartAndDestroyOld(): void
    {
        self::resetSessions();

        session_id(static::$sess1);

        $this->instance->start();

        $this->instance->restart(true);

        $newId = $this->instance->getId();

        $this->instance->writeClose();

        self::assertFileDoesNotExist(
            self::getSessionPath() . '/sess_' . static::$sess1,
        );

        self::assertEquals(
            'flower|s:6:"Sakura";animal|s:3:"Cat";',
            file_get_contents(self::getSessionPath() . '/sess_' . $newId),
        );
    }

    /**
     * @see  PhpBridge::getId
     * @runInSeparateProcess
     */
    public function testGetId(): void
    {
        session_id('qwe');

        self::assertEquals(
            'qwe',
            $this->instance->getId()
        );
    }

    /**
     * @see  PhpBridge::regenerate
     * @runInSeparateProcess
     */
    public function testRegenerate(): void
    {
        self::resetSessions();

        $this->instance->setId(static::$sess1);

        $this->instance->start();

        $this->instance->regenerate();

        $newId = $this->instance->getId();

        self::assertFileExists(self::getSessionPath() . '/' . 'sess_' . static::$sess1);

        self::assertNotEquals(
            static::$sess1,
            $newId,
        );
    }

    /**
     * @see  PhpBridge::regenerate
     * @runInSeparateProcess
     */
    public function testRegenerateDeleteOld(): void
    {
        self::resetSessions();

        $this->instance->setId(static::$sess1);

        $this->instance->start();

        $this->instance->regenerate(true);

        $newId = $this->instance->getId();

        self::assertFileDoesNotExist(self::getSessionPath() . '/' . 'sess_' . static::$sess1);

        self::assertNotEquals(
            static::$sess1,
            $newId,
        );
    }

    /**
     * @see  PhpBridge::getStorage
     */
    public function testGetStorage(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new PhpBridge();
    }

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    protected function tearDown(): void
    {
    }
}
