<?php
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */
declare(strict_types = 1);

namespace Windwalker\String\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\String\StringHelper;

/**
 * The StringHelperTest class.
 *
 * @since  {DEPLOY_VERSION}
 */
class StringHelperTest extends TestCase
{
    protected static $utf8String = 'fòôbàř';

    public function testAt()
    {
        self::assertEquals('ô', StringHelper::at('fòôbàř', 2));
        self::assertEquals('依', StringHelper::at('白日依山盡', 2));
    }

    /**
     * testBetween
     *
     * @param $string
     * @param $expected
     * @param $left
     * @param $right
     *
     * @return  void
     *
     * @dataProvider betweenProvider
     */
    public function testBetween($string, $expected, $left, $right, $offset = 0)
    {
        self::assertEquals($expected, StringHelper::between($string, $left, $right, $offset));
    }

    /**
     * betweenProvider
     *
     * @return  array
     */
    public function betweenProvider()
    {
        return [
            ['fòôbàř', 'ôb', 'ò', 'à'],
            ['To {be} or {not} to be', 'be', '{', '}'],
            ['To {be} or {not} to be', 'not', '{', '}', 4],
            ['To {{be} or {not} to be', '{be', '{', '}'],
            ['{foo} and {bar}', 'bar', '{', '}', 1],
        ];
    }

    /**
     * testCollapseWhitespace
     *
     * @return  void
     */
    public function testCollapseWhitespace()
    {
        self::assertEquals('foo bar yoo', StringHelper::collapseWhitespaces('foo  bar    yoo'));
        self::assertEquals('foo bar yoo', StringHelper::collapseWhitespaces('  foo  bar yoo '));
        self::assertEquals('foo bar yoo', StringHelper::collapseWhitespaces("  foo\n \r bar\n\r\n yoo \n"));
    }

    /**
     * testContains
     *
     * @param      $expected
     * @param      $string
     * @param      $search
     * @param bool $caseSensitive
     *
     * @dataProvider  containsProvider
     */
    public function testContains($expected, $string, $search, $caseSensitive = true)
    {
        self::assertSame($expected, StringHelper::contains($string, $search, $caseSensitive));
    }

    /**
     * containsProvider
     *
     * @return  array
     */
    public function containsProvider()
    {
        return [
            [true, 'foobar', 'oba'],
            [true, 'fooBar', 'oba', false],
            [false, 'fooBar', 'oba'],
            [true, 'fòôbàř', 'ôbà'],
            [true, '白日依山盡', '日依'],
            [false, '白日依山盡', '梅友仁'],
            [false, 'FÒÔbàř', 'ôbà'],
            [true, 'FÒÔbàř', 'ôbà', false],
        ];
    }

    public function testContainsAll()
    {
        self::markTestIncomplete();
    }

    public function testContainsAny()
    {
        self::markTestIncomplete();
    }

    public function testCount()
    {
        self::markTestIncomplete();
    }

    public function testCountSubstr()
    {
        self::markTestIncomplete();
    }

    public function testEndsWith()
    {
        self::markTestIncomplete();
    }

    public function testEnsureLeft()
    {
        self::markTestIncomplete();
    }

    public function testEnsureRight()
    {
        self::markTestIncomplete();
    }

    public function testFirst()
    {
        self::markTestIncomplete();
    }

    public function testHasLowerCase()
    {
        self::markTestIncomplete();
    }

    public function testHasUpperCase()
    {
        self::markTestIncomplete();
    }

    public function testInsert()
    {
        self::markTestIncomplete();
    }

    public function testIsJson()
    {
        self::markTestIncomplete();
    }

    public function testIsLowerCase()
    {
        self::markTestIncomplete();
    }

    public function testIsUpperCase()
    {
        self::markTestIncomplete();
    }

    public function testLast()
    {
        self::markTestIncomplete();
    }

    public function testLongestCommonPrefix()
    {
        self::markTestIncomplete();
    }

    public function testLongestCommonSuffix()
    {
        self::markTestIncomplete();
    }

    public function testLongestCommonSubstring()
    {
        self::markTestIncomplete();
    }

    public function testPad()
    {
        self::markTestIncomplete();
    }

    public function testPadBoth()
    {
        self::markTestIncomplete();
    }

    public function testPadLeft()
    {
        self::markTestIncomplete();
    }

    public function testPadRight()
    {
        self::markTestIncomplete();
    }

    public function testPrepend()
    {
        self::markTestIncomplete();
    }

    public function testRemoveLeft()
    {
        self::markTestIncomplete();
    }

    public function testRemoveRight()
    {
        self::markTestIncomplete();
    }

    public function testShuffle()
    {
        self::markTestIncomplete();
    }

    public function testStartsWith()
    {
        self::markTestIncomplete();
    }

    public function testSlice()
    {
        self::markTestIncomplete();
    }

    public function testSubstr()
    {
        self::markTestIncomplete();
    }

    public function testSurround()
    {
        self::markTestIncomplete();
    }

    public function testSwapCase()
    {
        self::markTestIncomplete();
    }

    public function testToLowerCase()
    {
        self::markTestIncomplete();
    }

    public function testToUpperCase()
    {
        self::markTestIncomplete();
    }

    public function testTruncate()
    {
        self::markTestIncomplete();
    }
}
