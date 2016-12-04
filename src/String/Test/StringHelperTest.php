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

    /**
     * testEndsWith
     *
     * @return  void
     *
     * @dataProvider endsWithProvider
     */
    public function testEndsWith($string, $search, $caseSensitive, $expected)
    {
        self::assertSame($expected, StringHelper::endsWith($string, $search, $caseSensitive));
    }

    /**
     * endsWithProvider
     *
     * @return  array
     */
    public function endsWithProvider()
    {
        return [
            ['Foo', 'oo', StringHelper::CASE_SENSITIVE, true],
            ['Foo', 'Oo', StringHelper::CASE_SENSITIVE, false],
            ['Foo', 'Oo', StringHelper::CASE_INSENSITIVE, true],
            ['Foo', 'ooooo', StringHelper::CASE_SENSITIVE, false],
            ['Foo', 'uv', StringHelper::CASE_SENSITIVE, false],
            ['黃河入海流', '入海流', StringHelper::CASE_SENSITIVE, true],
            ['黃河入海流', '入海流', StringHelper::CASE_INSENSITIVE, true],
            ['黃河入海流', '依山盡', StringHelper::CASE_SENSITIVE, false],
            ['FÒÔbà', 'ôbà', StringHelper::CASE_SENSITIVE, false],
            ['FÒÔbà', 'ôbà', StringHelper::CASE_INSENSITIVE, true],
        ];
    }

    /**
     * testStartsWith
     *
     * @param string $string
     * @param string $search
     * @param bool   $caseSensitive
     * @param bool   $expected
     *
     * @dataProvider estartsWithProvider
     */
    public function testStartsWith(string $string, string $search, bool $caseSensitive, bool $expected)
    {
        self::assertSame($expected, StringHelper::startsWith($string, $search, $caseSensitive));
    }

    /**
     * endsWithProvider
     *
     * @return  array
     */
    public function estartsWithProvider()
    {
        return [
            ['Foo', 'Fo', StringHelper::CASE_SENSITIVE, true],
            ['Foo', 'fo', StringHelper::CASE_SENSITIVE, false],
            ['Foo', 'fo', StringHelper::CASE_INSENSITIVE, true],
            ['Foo', 'foooo', StringHelper::CASE_SENSITIVE, false],
            ['Foo', 'uv', StringHelper::CASE_SENSITIVE, false],
            ['黃河入海流', '黃河', StringHelper::CASE_SENSITIVE, true],
            ['黃河入海流', '黃河', StringHelper::CASE_INSENSITIVE, true],
            ['黃河入海流', '依山盡', StringHelper::CASE_SENSITIVE, false],
            ['FÒÔbà', 'fò', StringHelper::CASE_SENSITIVE, false],
            ['FÒÔbà', 'fò', StringHelper::CASE_INSENSITIVE, true],
        ];
    }

    /**
     * testEnsureLeft
     *
     * @param string $string
     * @param string $search
     * @param string $expected
     *
     * @dataProvider ensureLeftProvider
     */
    public function testEnsureLeft(string $string, string $search, string $expected)
    {
        self::assertSame($expected, StringHelper::ensureLeft($string, $search));
    }

    /**
     * ensureLeftProvider
     *
     * @return  array
     */
    public function ensureLeftProvider()
    {
        return [
            ['FlowerSakura', 'Flower', 'FlowerSakura'],
            ['Sakura', 'Flower', 'FlowerSakura'],
            ['FlowerSakura', 'flower', 'flowerFlowerSakura'],
            ['黃河入海流', '黃河', '黃河入海流'],
            ['入海流', '黃河', '黃河入海流'],
            ['FÒÔbà', 'FÒÔ', 'FÒÔbà'],
            ['FÒÔbà', 'fòô', 'fòôFÒÔbà']
        ];
    }

    /**
     * testEnsureRight
     *
     * @param string $string
     * @param string $search
     * @param string $expected
     *
     * @return  void
     *
     * @dataProvider ensureRightProvider
     */
    public function testEnsureRight(string $string, string $search, string $expected)
    {
        self::assertSame($expected, StringHelper::ensureRight($string, $search));
    }

    /**
     * ensureRightProvider
     *
     * @return  array
     */
    public function ensureRightProvider()
    {
        return [
            ['FlowerSakura', 'Sakura', 'FlowerSakura'],
            ['Flower', 'Sakura', 'FlowerSakura'],
            ['FlowerSakura', 'sakura', 'FlowerSakurasakura'],
            ['黃河入海流', '海流', '黃河入海流'],
            ['黃河入', '海流', '黃河入海流'],
            ['FÒÔbà', 'Ôbà', 'FÒÔbà'],
            ['FÒÔbà', 'ôbà', 'FÒÔbàôbà']
        ];
    }

    /**
     * testHasLowerCase
     *
     * @param string $string
     * @param bool   $expected
     *
     * @return  void
     *
     * @dataProvider hasLowerCaseProvider
     */
    public function testHasLowerCase(string $string, bool $expected)
    {
        self::assertSame($expected, StringHelper::hasLowerCase($string));
    }

    /**
     * hasLowerCaseProvider
     *
     * @return  array
     */
    public function hasLowerCaseProvider()
    {
        return [
            ['Foo', true],
            ['FOO', false],
            ['FÒô', true],
            ['FÒÔ', false],
            ['白日依山盡', false]
        ];
    }

    /**
     * testHasUpperCase
     *
     * @param string $string
     * @param bool   $expected
     *
     * @return  void
     *
     * @dataProvider hasUpperCaseProvider
     */
    public function testHasUpperCase(string $string, bool $expected)
    {
        self::assertSame($expected, StringHelper::hasUpperCase($string));
    }

    /**
     * hasUpperCaseProvider
     *
     * @return  array
     */
    public function hasUpperCaseProvider()
    {
        return [
            ['Foo', true],
            ['foo', false],
            ['FÒô', true],
            ['fòô', false],
            ['白日依山盡', false]
        ];
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
