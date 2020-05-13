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

use Integrated\Common\Content\Form\Event\BuilderEvent;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BuilderEventTest extends FormEventTest
{
    /**
     * @var FormBuilderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    /**
     * @var array
     */
    protected $options = ['value 1', 'value 2', 'key' => 'value'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = $this->createMock('Symfony\\Component\\Form\\FormBuilderInterface');
    }

    public function testGetBuilder()
    {
        self::assertSame($this->builder, $this->getInstance()->getBuilder());
    }

    public function testGetField()
    {
        self::assertNull($this->getInstance()->getField());

        self::assertSame('field', $this->getInstance('field')->getField());
        self::assertSame('10', $this->getInstance(10)->getField());
    }

    public function testSetAndGetOptions()
    {
        $event = $this->getInstance();

        self::assertSame($this->options, $event->getOptions());

        $event->setOptions([]);

        self::assertSame([], $event->getOptions());
    }

    /**
     * @param mixed $field
     *
     * @return BuilderEvent
     */
    protected function getInstance($field = null)
    {
        return new BuilderEvent($this->type, $this->metadata, $this->builder, $this->options, $field);
    }
}
