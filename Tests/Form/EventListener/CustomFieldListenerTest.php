<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Form\EventListener;

use Integrated\Bundle\ContentBundle\Form\EventListener\CustomFieldListener;
use Integrated\Common\Content\Form\Events;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomFieldListener
     */
    protected $listener;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->listener = new CustomFieldListener();
    }

    /**
     * Test instanceOf
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
    }

    /**
     * Test getSubscribedEvents function
     */
    public function testGetSubscribedEventsFunction()
    {
        $this->assertSame(CustomFieldListener::getSubscribedEvents(), [Events::POST_BUILD => 'onPostBuild']);
    }

    /**
     * Test transform function with data
     */
    public function testOnPostBuildFunctionWithCustomFields()
    {
        /** @var \Integrated\Common\Content\Form\Event\BuilderEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('Integrated\Common\Content\Form\Event\BuilderEvent', [], [], '', false);

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $contentType */
        $contentType = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

        /** @var \Integrated\Common\Content\Form\Event\BuilderEvent|\PHPUnit_Framework_MockObject_MockObject $builder */
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field|\PHPUnit_Framework_MockObject_MockObject $field */
        $field = $this->getMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field');

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField|\PHPUnit_Framework_MockObject_MockObject $field */
        $customField = $this->getMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField');

        // Stub the getContentType function so it returns the contentType mock
        $event
            ->expects($this->once())
            ->method('getContentType')
            ->willReturn($contentType)
        ;

        // Stub the getBuilder function so it returns the builder mock
        $event
            ->expects($this->once())
            ->method('getBuilder')
            ->willReturn($builder)
        ;

        // Stub the getFields function so it returns an array with non custom fields
        $contentType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([$field, $customField])
        ;

        // Assert the add function, this function should be called
        $builder
            ->expects($this->once())
            ->method('add')
            ->with(
                CustomFieldListener::FORM_NAME,
                CustomFieldListener::FORM_TYPE,
                [
                    'contentType' => $contentType,
                    // TODO this will be mapped in INTEGRATED-552
                    'mapped' => false
                ]
            )
        ;

        // Fire the event
        $this->listener->onPostBuild($event);
    }
}
