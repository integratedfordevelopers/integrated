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

    protected function setUp()
    {
        parent::setUp();

        $this->builder = $this->getMock('Symfony\\Component\\Form\\FormBuilderInterface');
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

        self::assertSame([], $event->getOptions());

        $options = ['value 1', 'value 2', 'key' => 'value'];
        $event->setOptions($options);

        self::assertSame($options, $event->getOptions());
    }

    /**
     * @param mixed $field
     *
     * @return BuilderEvent
     */
    protected function getInstance($field = null)
    {
        return new BuilderEvent($this->type, $this->metadata, $this->builder, $field);
    }
}
