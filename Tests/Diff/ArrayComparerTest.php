<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Diff;

use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ArrayComparerTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testAddValue()
    {
        $this->assertDiff(
            [], // old
            ['key' => 'value'], // new
            ['key' => 'value'] // expected
        );
    }

    /**
     */
    public function testUpdateValue()
    {
        $this->assertDiff(
            ['key' => 'undefined', 'key2' => 'unchanged'], // old
            ['key' => 'value', 'key2' => 'unchanged'], // new
            ['key' => 'value'] // expected
        );
    }

    /**
     */
    public function testRemoveValue()
    {
        $this->assertDiff(
            ['undefined' => 'test'], // old
            [], // new
            ['undefined' => null] // expected
        );

        $this->assertDiff(
            [], // old
            ['empty' => []], // new
            [] // expected
        );
    }

    /**
     * @param array $old
     * @param array $new
     * @param array $expected
     */
    protected function assertDiff(array $old = [], array $new = [], array $expected = [])
    {
        $this->assertEquals($expected, ArrayComparer::diff($old, $new));
    }
}
