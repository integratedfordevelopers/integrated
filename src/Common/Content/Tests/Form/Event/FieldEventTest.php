<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Form\Event;

use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Form\Mapping\AttributeEditorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldEventTest extends FormEventTest
{
    /**
     * @var AttributeEditorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $field;

    /**
     * @var array
     */
    protected $options = ['value 1', 'value 2', 'key' => 'value'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = $this->createMock(AttributeEditorInterface::class);
    }

    public function testGetField()
    {
        self::assertSame($this->field, $this->getInstance()->getField());
    }

    public function testGetOptions()
    {
        self::assertSame($this->options, $this->getInstance()->getOptions());
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

    /**
     * @return FieldEvent
     */
    protected function getInstance()
    {
        return new FieldEvent($this->type, $this->metadata, $this->field, $this->options);
    }
}
