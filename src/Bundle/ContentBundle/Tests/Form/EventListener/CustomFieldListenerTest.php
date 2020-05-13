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

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ContentBundle\Form\EventListener\CustomFieldListener;
use Integrated\Bundle\ContentBundle\Form\Type\CustomFieldsType;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Events;
use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomFieldListener
     */
    protected $listener;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->listener = new CustomFieldListener();
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
    }

    /**
     * Test getSubscribedEvents function.
     */
    public function testGetSubscribedEventsFunction()
    {
        $this->assertSame(CustomFieldListener::getSubscribedEvents(), [Events::POST_BUILD => 'onPostBuild']);
    }

    /**
     * Test onPostBuild event with custom fields.
     */
    public function testOnPostBuildFunctionWithCustomFields()
    {
        /** @var BuilderEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(BuilderEvent::class)->disableOriginalConstructor()->getMock();

        /** @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $contentType */
        $contentType = $this->createMock(ContentTypeInterface::class);

        /** @var FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject $builder */
        $builder = $this->createMock(FormBuilderInterface::class);

        /** @var Field|\PHPUnit_Framework_MockObject_MockObject $field */
        $field = $this->createMock(Field::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField */
        $customField = $this->createMock(CustomField::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField2 */
        $customField2 = $this->createMock(CustomField::class);

        $event
            ->expects($this->once())
            ->method('getContentType')
            ->willReturn($contentType)
        ;

        $event
            ->expects($this->once())
            ->method('getBuilder')
            ->willReturn($builder)
        ;

        $contentType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([$field, $customField, $customField2])
        ;

        // Assert the add function, this function should be called once
        $builder
            ->expects($this->once())
            ->method('add')
            ->with(
                CustomFieldListener::FORM_NAME,
                CustomFieldsType::class,
                [
                    'contentType' => $contentType,
                ]
            )
        ;

        $this->listener->onPostBuild($event);
    }

    /**
     * Test onPostBuild event with no custom fields.
     */
    public function testOnPostBuildFunctionWithNoCustomFields()
    {
        /** @var BuilderEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(BuilderEvent::class)->disableOriginalConstructor()->getMock();

        /** @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $contentType */
        $contentType = $this->createMock(ContentTypeInterface::class);

        /** @var FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject $builder */
        $builder = $this->createMock(FormBuilderInterface::class);

        /** @var Field|\PHPUnit_Framework_MockObject_MockObject $field */
        $field = $this->createMock(Field::class);

        $event
            ->expects($this->once())
            ->method('getContentType')
            ->willReturn($contentType)
        ;

        $event
            ->expects($this->never())
            ->method('getBuilder')
            ->willReturn($builder)
        ;

        $contentType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([$field])
        ;

        // Assert the add function, this function should not be called
        $builder
            ->expects($this->never())
            ->method('add')
        ;

        $this->listener->onPostBuild($event);
    }
}
