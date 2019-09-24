<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Tests\Diff;

use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ArrayComparerTest extends \PHPUnit\Framework\TestCase
{
    public function testAddField()
    {
        $this->assertDiff(
            [], // old
            ['key' => 'value'], // new
            ['key' => [null, 'value']] // expected
        );
    }

    public function testRemoveField()
    {
        $this->assertDiff(
            ['key2' => 'value2'], // old
            [], // new
            ['key2' => ['value2', null]] // expected
        );
    }

    public function testUpdateValue()
    {
        $this->assertDiff(
            ['key3' => 'value3', 'key4' => 'unchanged'], // old
            ['key3' => 'value4', 'key4' => 'unchanged'], // new
            ['key3' => ['value3', 'value4']] // expected
        );
    }

    public function testEmptyValue()
    {
        $this->assertDiff(
            [], // old
            ['key5' => []], // new
            [] // expected
        );

        $this->assertDiff(
            [], // old
            ['key6' => null], // new
            [] // expected
        );

        $this->assertDiff(
            ['key7' => null], // old
            [], // new
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
