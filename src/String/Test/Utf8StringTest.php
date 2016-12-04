<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT Taiwan, Inc. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\Utilities\Test;

use \Windwalker\String\Utf8String;

/**
 * Test class of String
 *
 * @since 2.0
 */
class Utf8StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    String
     * @since  2.0
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function isAsciiProvider()
    {
        return array(
            array('ascii', true),
            array('1024', true),
            array('#$#@$%', true),
            array('áÑ', false),
            array('ÿ©', false),
            array('¡¾', false),
            array('÷™', false),
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function strposProvider()
    {
        return array(
            array(3, 'missing', 'sing', 0),
            array(false, 'missing', 'sting', 0),
            array(4, 'missing', 'ing', 0),
            array(10, ' объектов на карте с', 'на карте', 0),
            array(0, 'на карте с', 'на карте', 0, 0),
            array(false, 'на карте с', 'на каррте', 0),
            array(false, 'на карте с', 'на карте', 2),
            array(3, 'missing', 'sing', 0)
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestGetStrrpos()
    {
        return array(
            array(3, 'missing', 'sing', 0),
            array(false, 'missing', 'sting', 0),
            array(4, 'missing', 'ing', 0),
            array(10, ' объектов на карте с', 'на карте', 0),
            array(0, 'на карте с', 'на карте', 0),
            array(false, 'на карте с', 'на каррте', 0),
            array(3, 'на карте с', 'карт', 2)
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestSubstr()
    {
        return array(
            array('issauga', 'Mississauga', 4, null),
            array('на карте с', ' объектов на карте с', 10, null),
            array('на ка', ' объектов на карте с', 10, 5),
            array('те с', ' объектов на карте с', -4, null),
            array(false, ' объектов на карте с', 99, null)
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrtolower()
    {
        return array(
            array('Windwalker! Rocks', 'windwalker! rocks')
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrtoupper()
    {
        return array(
            array('Windwalker! Rocks', 'WINDWALKER! ROCKS')
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrlen()
    {
        return array(
            array('Windwalker! Rocks', 17)
        );
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStr_ireplace()
    {
        return [
            ['Pig', 'cow', 'the pig jumped', null, 'the cow jumped'],
            ['Pig', 'cow', 'the pig jumped', 1, 'the cow jumped'],
            ['Pig', 'cow', 'the pig jumped over the cow', 1, 'the cow jumped over the cow'],
            [
                ['PIG', 'JUMPED'],
                ['cow', 'hopped'],
                'the pig jumped over the pig',
                null,
                'the cow hopped over the cow'
            ],
            ['шил', 'биш', 'Би шил идэй чадна', 1, 'Би биш идэй чадна'],
            ['/', ':', '/test/slashes/', null, ':test:slashes:'],
            ['/', ':', '/test/slashes/', 1, ':test/slashes/'],
            ['', ':', '/test/slashes/', null, '/test/slashes/'],
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStr_split()
    {
        return [
            ['string', 1, ['s', 't', 'r', 'i', 'n', 'g']],
            ['string', 2, ['st', 'ri', 'ng']],
            ['волн', 3, ['вол', 'н']],
            ['волн', 1, ['в', 'о', 'л', 'н']],
            ['волн', 0, false]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrcasecmp()
    {
        return [
            ['THIS IS STRING1', 'this is string1', 0],
            ['this is string1', 'this is string2', -1],
            ['this is string2', 'this is string1', 1],
            ['бгдпт', 'бгдпт', 0],
            ['àbc', 'abc', 1]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrcmp()
    {
        return [
            ['THIS IS STRING1', 'this is string1', -1],
            ['this is string1', 'this is string2', -1],
            ['this is string2', 'this is string1', 1],
            ['a', 'B', 1],
            ['A', 'b', -1]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrcspn()
    {
        return [
            ['subject <a> string <a>', '<>', 0, null, 8],
            ['Би шил {123} идэй {456} чадна', '}{', 0, null, 7],
            ['Би шил {123} идэй {456} чадна', '}{', 13, 10, 5],
            ['Би шил {123} идэй {456} чадна', '', 13, 10, 0],
            ['Not contains', '}{', 13, 10, 0],
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStristr()
    {
        return [
            ['haystack', 'needle', false],
            ['before match, after match', 'match', 'match, after match'],
            ['Би шил идэй чадна', 'шил', 'шил идэй чадна']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrrev()
    {
        return [
            ['abc def', 'fed cba'],
            ['Би шил', 'лиш иБ']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestStrspn()
    {
        return [
            ['A321 Main Street', '0123456789', 1, 2, 2],
            ['321 Main Street', '0123456789', 0, 2, 2],
            ['A321 Main Street', '0123456789', 0, 10, 0],
            ['321 Main Street', '0123456789', 0, null, 3],
            ['Main Street 321', '0123456789', 0, -3, 0],
            ['321 Main Street', '0123456789', 0, -13, 2],
            ['321 Main Street', '0123456789', 0, -12, 3],
            ['A321 Main Street', '0123456789', 0, null, 0],
            ['A321 Main Street', '0123456789', 1, 10, 3],
            ['A321 Main Street', '0123456789', 1, null, 3],
            ['Би шил идэй чадна', 'Би', 0, null, 2],
            ['чадна Би шил идэй чадна', 'Би', 0, null, 0]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function substrReplaceProvider()
    {
        return [
            ['321 Broadway Avenue', '321 Main Street', 'Broadway Avenue', 4, null],
            ['321 Broadway Street', '321 Main Street', 'Broadway', 4, 4],
            ['чадна 我能吞', 'чадна Би шил идэй чадна', '我能吞', 6, null],
            ['чадна 我能吞 шил идэй чадна', 'чадна Би шил идэй чадна', '我能吞', 6, 2]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestLtrim()
    {
        return [
            ['   abc def', null, 'abc def'],
            ['   abc def', '', '   abc def'],
            [' Би шил', null, 'Би шил'],
            ["\t\n\r\x0BБи шил", null, 'Би шил'],
            ["\x0B\t\n\rБи шил", "\t\n\x0B", "\rБи шил"],
            ["\x09Би шил\x0A", "\x09\x0A", "Би шил\x0A"],
            ['1234abc', '0123456789', 'abc']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestRtrim()
    {
        return [
            ['abc def   ', null, 'abc def'],
            ['abc def   ', '', 'abc def   '],
            ['Би шил ', null, 'Би шил'],
            ["Би шил\t\n\r\x0B", null, 'Би шил'],
            ["Би шил\r\x0B\t\n", "\t\n\x0B", "Би шил\r"],
            ["\x09Би шил\x0A", "\x09\x0A", "\x09Би шил"],
            ['1234abc', 'abc', '01234']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestTrim()
    {
        return [
            ['  abc def   ', null, 'abc def'],
            ['  abc def   ', '', '  abc def   '],
            ['   Би шил ', null, 'Би шил'],
            ["\t\n\r\x0BБи шил\t\n\r\x0B", null, 'Би шил'],
            ["\x0B\t\n\rБи шил\r\x0B\t\n", "\t\n\x0B", "\rБи шил\r"],
            ["\x09Би шил\x0A", "\x09\x0A", "Би шил"],
            ['1234abc56789', '0123456789', 'abc']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestUcfirst()
    {
        return [
            ['george', null, null, 'George'],
            ['мога', null, null, 'Мога'],
            ['ψυχοφθόρα', null, null, 'Ψυχοφθόρα'],
            ['', null, null, ''],
            ['ψ', null, null, 'Ψ'],
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestUcwords()
    {
        return [
            ['george washington', 'George Washington'],
            ["george\r\nwashington", "George\r\nWashington"],
            ['мога', 'Мога'],
            ['αβγ δεζ', 'Αβγ Δεζ'],
            ['åbc öde', 'Åbc Öde']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestConvertEncoding()
    {
        return [
            ['Åbc Öde €2.0', 'UTF-8', 'ISO-8859-15', "\xc5bc \xd6de \xA42.0"],
            ['', 'UTF-8', 'ISO-8859-15', '']
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestValid()
    {
        return [
            ["\xCF\xB0", true],
            ["\xFBa", false],
            ["\xFDa", false],
            ["foo\xF7bar", false],
            ['george Мога Ž Ψυχοφθόρα ฉันกินกระจกได้ 我能吞下玻璃而不伤身体 ', true],
            ["\xFF ABC", false],
            ["\xFa ABC", false],
            ["0xfffd ABC", true],
            ['', true]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestUnicodeToUtf8()
    {
        return [
            ["\u0422\u0435\u0441\u0442 \u0441\u0438\u0441\u0442\u0435\u043c\u044b", "Тест системы"],
            ["\u00dcberpr\u00fcfung der Systemumstellung", "Überprüfung der Systemumstellung"]
        ];
    }

    /**
     * Test...
     *
     * @return  array
     *
     * @since   2.0
     */
    public function seedTestUnicodeToUtf16()
    {
        return [
            ["\u0422\u0435\u0441\u0442 \u0441\u0438\u0441\u0442\u0435\u043c\u044b", "Тест системы"],
            ["\u00dcberpr\u00fcfung der Systemumstellung", "Überprüfung der Systemumstellung"]
        ];
    }

    /**
     * testCallStatic
     *
     * @return  void
     */
    public function testCallStatic()
    {
        $this->expectException(\BadMethodCallException::class);

        Utf8String::noexists('test');
    }

    /**
     * Test...
     *
     * @param   string  $string   @todo
     * @param   boolean $expected @todo
     *
     * @return  void
     *
     * @dataProvider  isAsciiProvider
     * @since         2.0
     */
    public function testIsAscii($string, $expected)
    {
        $this->assertEquals(
            $expected,
            Utf8String::isAscii($string)
        );
    }

    /**
     * Test...
     *
     * @param   string  $expect   @todo
     * @param   string  $haystack @todo
     * @param   string  $needle   @todo
     * @param   integer $offset   @todo
     *
     * @return  void
     *
     * @dataProvider  strposProvider
     * @since         2.0
     */
    public function testStrpos($expect, $haystack, $needle, $offset = 0)
    {
        $actual = Utf8String::strpos($haystack, $needle, $offset);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string  $expect   @todo
     * @param   string  $haystack @todo
     * @param   string  $needle   @todo
     * @param   integer $offset   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestGetStrrpos
     * @since         2.0
     */
    public function testStrrpos($expect, $haystack, $needle, $offset = 0)
    {
        $actual = Utf8String::strrpos($haystack, $needle, $offset);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string   $expect @todo
     * @param   string   $string @todo
     * @param   string   $start  @todo
     * @param   bool|int $length @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestSubstr
     * @since         2.0
     */
    public function testSubstr($expect, $string, $start, $length = null)
    {
        $actual = Utf8String::substr($string, $start, $length);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrtolower
     * @since         2.0
     */
    public function testStrtolower($string, $expect)
    {
        $actual = Utf8String::strtolower($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrtoupper
     * @since         2.0
     */
    public function testStrtoupper($string, $expect)
    {
        $actual = Utf8String::strtoupper($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrlen
     * @since         2.0
     */
    public function testStrlen($string, $expect)
    {
        $actual = Utf8String::strlen($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string  $search  @todo
     * @param   string  $replace @todo
     * @param   string  $subject @todo
     * @param   integer $count   @todo
     * @param   string  $expect  @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStr_ireplace
     * @since         2.0
     */
    public function testStr_ireplace($search, $replace, $subject, $count, $expect)
    {
        $actual = Utf8String::str_ireplace($search, $replace, $subject, $count);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string       @todo
     * @param   string $split_length @todo
     * @param   string $expect       @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStr_split
     * @since         2.0
     */
    public function testStr_split($string, $split_length, $expect)
    {
        $actual = Utf8String::str_split($string, $split_length);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string1 @todo
     * @param   string $string2 @todo
     * @param   string $locale  @todo
     * @param   string $expect  @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrcasecmp
     * @since         2.0
     */
    public function testStrcasecmp($string1, $string2, $expect)
    {
        $actual = Utf8String::strcasecmp($string1, $string2);

        if ($actual !== 0) {
            $actual /= abs($actual);
        }

        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string1 @todo
     * @param   string $string2 @todo
     * @param   string $locale  @todo
     * @param   string $expect  @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrcmp
     * @since         2.0
     */
    public function testStrcmp($string1, $string2, $expect)
    {
        $actual = Utf8String::strcmp($string1, $string2);

        if ($actual !== 0) {
            $actual /= abs($actual);
        }

        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string  $haystack @todo
     * @param   string  $needles  @todo
     * @param   integer $start    @todo
     * @param   integer $len      @todo
     * @param   string  $expect   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrcspn
     * @since         2.0
     */
    public function testStrcspn($haystack, $needles, $start, $len, $expect)
    {
        $actual = Utf8String::strcspn($haystack, $needles, $start, $len);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $haystack @todo
     * @param   string $needle   @todo
     * @param   string $expect   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStristr
     * @since         2.0
     */
    public function testStristr($haystack, $needle, $expect)
    {
        $actual = Utf8String::stristr($haystack, $needle);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrrev
     * @since         2.0
     */
    public function testStrrev($string, $expect)
    {
        $actual = Utf8String::strrev($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string  $subject @todo
     * @param   string  $mask    @todo
     * @param   integer $start   @todo
     * @param   integer $length  @todo
     * @param   string  $expect  @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestStrspn
     * @since         2.0
     */
    public function testStrspn($subject, $mask, $start, $length, $expect)
    {
        $actual = Utf8String::strspn($subject, $mask, $start, $length);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string  $expect      @todo
     * @param   string  $string      @todo
     * @param   string  $replacement @todo
     * @param   integer $start       @todo
     * @param   integer $length      @todo
     *
     * @return  array
     *
     * @dataProvider  substrReplaceProvider
     * @since         2.0
     */
    public function testSubstrReplace($expect, $string, $replacement, $start, $length)
    {
        $actual = Utf8String::substrReplace($string, $replacement, $start, $length);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string   @todo
     * @param   string $charlist @todo
     * @param   string $expect   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestLtrim
     * @since         2.0
     */
    public function testLtrim($string, $charlist, $expect)
    {
        if ($charlist === null) {
            $actual = Utf8String::ltrim($string);
        } else {
            $actual = Utf8String::ltrim($string, $charlist);
        }

        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string   @todo
     * @param   string $charlist @todo
     * @param   string $expect   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestRtrim
     * @since         2.0
     */
    public function testRtrim($string, $charlist, $expect)
    {
        if ($charlist === null) {
            $actual = Utf8String::rtrim($string);
        } else {
            $actual = Utf8String::rtrim($string, $charlist);
        }

        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string   @todo
     * @param   string $charlist @todo
     * @param   string $expect   @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestTrim
     * @since         2.0
     */
    public function testTrim($string, $charlist, $expect)
    {
        if ($charlist === null) {
            $actual = Utf8String::trim($string);
        } else {
            $actual = Utf8String::trim($string, $charlist);
        }

        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string       @todo
     * @param   string $delimiter    @todo
     * @param   string $newDelimiter @todo
     * @param   string $expect       @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestUcfirst
     * @since         2.0
     */
    public function testUcfirst($string, $delimiter, $newDelimiter, $expect)
    {
        $actual = Utf8String::ucfirst($string, $delimiter, $newDelimiter);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestUcwords
     * @since         2.0
     */
    public function testUcwords($string, $expect)
    {
        $actual = Utf8String::ucwords($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * testSubstr_count
     *
     * @param $string
     * @param $search
     * @param $expected
     * @param $caseSensitive
     *
     * @dataProvider substr_countProvider
     */
    public function testSubstr_count($string, $search, $expected, $caseSensitive)
    {
        self::assertEquals($expected, Utf8String::substrCount($string, $search, $caseSensitive));
    }

    /**
     * substr_countProvider
     *
     * @return  array
     */
    public function substr_countProvider()
    {
        return [
            ['FooBarFlowerSakura', 'Flower', 1, Utf8String::CASE_SENSITIVE],
            ['FooBarFlowerSakura', 'o', 3, Utf8String::CASE_SENSITIVE],
            ['FooOOooo', 'o', 5, Utf8String::CASE_SENSITIVE],
            ['FooOOooo', 'o', 7, Utf8String::CASE_INSENSITIVE],
            ['FÒÔòôòô', 'ô', 2, Utf8String::CASE_SENSITIVE],
            ['FÒÔòôòô', 'ô', 3, Utf8String::CASE_INSENSITIVE],
            ['объектов на карте с', 'б', 1, Utf8String::CASE_SENSITIVE],
            ['庭院深深深幾許', '深', 3, Utf8String::CASE_SENSITIVE]
        ];
    }

    /**
     * Test...
     *
     * @param   string $source        @todo
     * @param   string $from_encoding @todo
     * @param   string $to_encoding   @todo
     * @param   string $expect        @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestConvertEncoding
     * @since         2.0
     */
    public function testConvertEncoding($source, $from_encoding, $to_encoding, $expect)
    {
        $actual = Utf8String::convertEncoding($source, $from_encoding, $to_encoding);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestValid
     * @since         2.0
     */
    public function testValid($string, $expect)
    {
        $actual = Utf8String::isUtf8($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *

     * @dataProvider  seedTestUnicodeToUtf8
     * @since         2.0
     */
    public function testUnicodeToUtf8($string, $expect)
    {
        $actual = Utf8String::unicodeToUtf8($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestUnicodeToUtf16
     * @since         2.0
     */
    public function testUnicodeToUtf16($string, $expect)
    {
        $actual = Utf8String::unicodeToUtf16($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * Test...
     *
     * @param   string $string @todo
     * @param   string $expect @todo
     *
     * @return  array
     *
     * @dataProvider  seedTestValid
     * @since         2.0
     */
    public function testCompliant($string, $expect)
    {
        $actual = Utf8String::compliant($string);
        $this->assertEquals($expect, $actual);
    }

    /**
     * testShuffle
     *
     * @return  void
     *
     * @dataProvider providerTestShuffle
     */
    public function testShuffle($string)
    {
        $result = Utf8String::shuffle($string);

        self::assertNotEquals($result, $string);
        self::assertEquals(strlen($result), strlen($string));

        $len = mb_strlen($string);

        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($string, $i, 1);
            $countBefore = mb_substr_count($string, $char);
            $countAfter = mb_substr_count($result, $char);

            self::assertEquals($countBefore, $countAfter);
        }
    }

    /**
     * providerTestShuffle
     *
     * @return  array
     */
    public function providerTestShuffle()
    {
        return [
            ['foo bar'],
            ['∂∆ ˚åß'],
            ['å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬']
        ];
    }
}
