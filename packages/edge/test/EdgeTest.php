<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Edge\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\DOM\Test\DOMTestTrait;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Loader\EdgeFileLoader;

/**
 * Test class of Edge
 *
 * @since 3.0
 */
class EdgeTest extends TestCase
{
    use DOMTestTrait;

    /**
     * Test instance.
     *
     * @var Edge
     */
    protected ?Edge $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = new Edge(
            new EdgeFileLoader(
                [
                    __DIR__ . '/tmpl',
                ]
            )
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Method to test render().
     *
     * @return void
     *
     * @throws \Windwalker\Edge\Exception\EdgeException
     * @covers \Windwalker\Edge\Edge::render
     */
    public function testRender()
    {
        $result = $this->instance->render('hello');

        $expected = <<<HTML
<html>
    <body>
        This is the master sidebar.

        <p>This is appended to the master sidebar.</p>

        <div class="container">
            <p>This is my body content.</p>
            A
        </div>
    </body>
</html>
HTML;

        $this->assertHtmlFormatEquals($expected, $result);
    }
}
