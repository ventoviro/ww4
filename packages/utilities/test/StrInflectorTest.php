<?php

/**
 * @copyright  Copyright (C) 2019 LYRASOFT Source Matters, Inc.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Utilities\StrInflector;

/**
 * Test for the StrInflector class.
 *
 * @link   http://en.wikipedia.org/wiki/English_plural
 * @since  2.0
 */
class StrInflectorTest extends TestCase
{
    /**
     * Method to provider data to testIsCountable.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function providerIsCountable()
    {
        return [
            ['id', true],
            ['item', true],
            ['title', false],
        ];
    }

    /**
     * Method to provider data to testToPlural.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function providerSinglePlural()
    {
        return [
            // Regular plurals
            ['bus', 'buses'],
            ['notify', 'notifies'],
            ['click', 'clicks'],

            // Almost regular plurals.
            ['photo', 'photos'],
            ['zero', 'zeros'],

            // Irregular identicals
            ['salmon', 'salmon'],

            // Irregular plurals
            ['ox', 'oxen'],
            ['quiz', 'quizzes'],
            ['status', 'statuses'],
            ['matrix', 'matrices'],
            ['index', 'indices'],
            ['vertex', 'vertices'],
            ['hive', 'hives'],

            // Ablaut plurals
            ['foot', 'feet'],
            ['goose', 'geese'],
            ['louse', 'lice'],
            ['man', 'men'],
            ['mouse', 'mice'],
            ['tooth', 'teeth'],
            ['woman', 'women'],
        ];
    }

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return  void
     *
     * @since   2.0
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Method to test StrInflector::isPlural().
     *
     * @param  string  $singular  The singular form of a word.
     * @param  string  $plural    The plural form of a word.
     *
     * @return  void
     *
     * @dataProvider  providerSinglePlural
     */
    public function testIsPlural($singular, $plural)
    {
        $this->assertTrue(
            StrInflector::isPlural($plural),
            'Checks the plural is a plural.'
        );

        if ($singular != $plural) {
            $this->assertFalse(
                StrInflector::isPlural($singular),
                'Checks the singular is not plural.'
            );
        }
    }

    /**
     * Method to test StrInflector::isSingular().
     *
     * @param  string  $singular  The singular form of a word.
     * @param  string  $plural    The plural form of a word.
     *
     * @return  void
     *
     * @dataProvider  providerSinglePlural
     */
    public function testIsSingular($singular, $plural)
    {
        $this->assertTrue(
            StrInflector::isSingular($singular),
            'Checks the singular is a singular.'
        );

        if ($singular != $plural) {
            $this->assertFalse(
                StrInflector::isSingular($plural),
                sprintf(
                    'Checks the plural (%s) is not singular.',
                    $plural
                )
            );
        }
    }

    /**
     * Method to test StrInflector::toPlural().
     *
     * @param  string  $singular  The singular form of a word.
     * @param  string  $plural    The plural form of a word.
     *
     * @return  void
     *
     * @dataProvider  providerSinglePlural
     */
    public function testToPlural($singular, $plural)
    {
        $this->assertThat(
            StrInflector::toPlural($singular),
            $this->equalTo($plural)
        );
    }

    /**
     * Method to test StrInflector::toPlural().
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testToPluralAlreadyPlural()
    {
        $this->assertEquals('buses', StrInflector::toPlural('buses'));
    }

    /**
     * Method to test StrInflector::toPlural().
     *
     * @param  string  $singular  The singular form of a word.
     * @param  string  $plural    The plural form of a word.
     *
     * @return  void
     *
     * @dataProvider  providerSinglePlural
     */
    public function testToSingular($singular, $plural)
    {
        $this->assertThat(
            StrInflector::toSingular($plural),
            $this->equalTo($singular)
        );
    }

    /**
     * Method to test StrInflector::toPlural().
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testToSingularRetFalse()
    {
        // Assertion for already singular
        $this->assertEquals('bus', StrInflector::toSingular('bus'));

        $this->assertEquals('foo', StrInflector::toSingular('foo'));
    }
}
