<?php

/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Utilities\SimpleTemplate;

/**
 * Test class of SimpleTemplate
 *
 * @since 3.0
 */
class SimpleTemplateTest extends TestCase
{
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
     */
    public function testRender()
    {
        $data['foo']['bar']['baz'] = 'Flower';

        $this->assertEquals('This is Flower', SimpleTemplate::render('This is {{ foo.bar.baz }}', $data));
        $this->assertEquals('This is ', SimpleTemplate::render('This is {{ foo.yoo }}', $data));
    }

    public function testRenderTemplate(): void
    {
        $data['foo']['bar']['baz'] = 'Flower';

        $this->assertEquals(
            'This is Flower',
            SimpleTemplate::create()
                ->setDelimiter('/')
                ->setVarWrapper('[', ']')
                ->renderTemplate('This is [ foo/bar/baz ]', $data)
        );
    }
}
