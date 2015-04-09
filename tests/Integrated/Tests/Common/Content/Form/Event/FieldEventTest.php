<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form\Event;

use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\ContentType\ContentTypeFieldInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldEventTest extends FormEventTest
{
    public function testSetAndGetField()
    {
        $event = $this->getInstance();

        self::assertNull($event->getField());

        $event->setField($field = $this->getField());

        self::assertSame($field, $event->getField());
    }

    public function testSetAndGetIgnore()
    {
        $event = $this->getInstance();

        self::assertFalse($event->isIgnored());

        $event->setIgnore(true);

        self::assertTrue($event->isIgnored());

        $event->setIgnore(false);

        self::assertFalse($event->isIgnored());
    }

    public function testSetAndGetOptions()
    {
        $event = $this->getInstance();

        self::assertSame([], $event->getOptions());

        $options = ['value 1', 'value 2', 'key' => 'value'];
        $event->setOptions($options);

        self::assertSame($options, $event->getOptions());
    }

    /**
     * @return FieldEvent
     */
    protected function getInstance()
    {
        return new FieldEvent($this->type, $this->metadata);
    }

    /**
     * @return ContentTypeFieldInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getField()
    {
        return $this->getMock('Integrated\\Common\\ContentType\\ContentTypeFieldInterface');
    }
}
