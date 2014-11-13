<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType;

use Integrated\Common\ContentType\Iterator;

/**
 * The iterator is nothing more then a array iterator that implements the IteratorInterface
 * interface to give some extra code completion. So just check for that and don't write test
 * to test the spl array iterator implementation.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $this->getInstance());
        self::assertInstanceOf('ArrayIterator', $this->getInstance());
    }

    /**
     * @return Iterator
     */
    protected function getInstance()
    {
        return new Iterator();
    }
}
