<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Form;

use Integrated\Common\Content\Form\ContentFormType;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Event\ViewEvent;
use Integrated\Common\Content\Form\Events;
use Integrated\Common\ContentType\ContentTypeFieldInterface;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Form\Mapping\AttributeEditorInterface;
use Integrated\Common\Form\Mapping\AttributeInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @covers \Integrated\Common\Content\Form\ContentFormType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentFormTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $type;

    /**
     * @var MetadataFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataFactory;

    /**
     * @var MetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

    /**
     * @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->type = $this->createMock(ContentTypeInterface::class);
        $this->metadataFactory = $this->createMock(MetadataFactoryInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);

        $this->metadataFactory
            ->expects($this->any())
            ->method('getMetadata')
            ->willReturn($this->metadata)
        ;

        $this->resolver = $this->createMock(ResolverInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(AbstractType::class, $this->getInstance());
    }

    public function testBuildForm()
    {
        $this->dispatcher->expects($this->atLeastOnce())
            ->method('hasListeners')
            ->willReturn(false);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([
                $this->getAttribute('field1', 'type1', ['options1', 'options' => '1']),
                $this->getAttribute('field2', 'type2', ['options2', 'options' => '2']),
                $this->getAttribute('field3', 'type3', ['options3', 'options' => '3']),
            ]);

        $this->type->expects($this->exactly(3))
            ->method('hasField')
            ->withConsecutive(
                [$this->equalTo('field1')],
                [$this->equalTo('field2')],
                [$this->equalTo('field3')]
            )
            ->willReturnOnConsecutiveCalls(true, false, true);

        $this->type->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('field1')],
                [$this->equalTo('field3')]
            )
            ->willReturnOnConsecutiveCalls(
                $this->getField('field1', ['override1']),
                $this->getField('field3', ['override3'])
            );

        $builder = $this->getBuilder();
        $builder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo('field1'), $this->equalTo('type1'), $this->equalTo(['override1', 'options' => '1'])],
                [$this->equalTo('field3'), $this->equalTo('type3'), $this->equalTo(['override3', 'options' => '3'])]
            );

        $this->getInstance()->buildForm($builder, ['content_type' => $this->type]);
    }

    public function testBuildFormEventDispatcher()
    {
        $builder = $this->getBuilder();
        $field = $this->getField('field');

        $callback = [
            function (BuilderEvent $argument) use ($builder) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame($builder, $argument->getBuilder());
                self::assertSame(['key' => 'value'], $argument->getOptions());
                self::assertNull($argument->getField());

                return true;
            },
            function (BuilderEvent $argument) use ($builder) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame($builder, $argument->getBuilder());
                self::assertSame(['key' => 'value'], $argument->getOptions());
                self::assertSame('field', $argument->getField());

                return true;
            },
            function (FieldEvent $argument) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame(['key' => 'value'], $argument->getOptions());
                self::assertInstanceOf(AttributeEditorInterface::class, $argument->getField());
                self::assertSame('field', $argument->getField()->getName());
                self::assertSame('type', $argument->getField()->getType());
                self::assertSame(['key' => 'value'], $argument->getField()->getOptions());

                return true;
            },
        ];

        // first test events for field that does exit in the content type followed by a field
        // that does not exist in the content type. The only difference should be that the later
        // should not trigger a FieldEvent.
        //
        // And yes the field name is reused.

        $this->dispatcher->expects($this->exactly(7))
            ->method('hasListeners')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_BUILD)],
                [$this->equalTo(Events::PRE_BUILD_FIELD)],
                [$this->equalTo(Events::BUILD_FIELD)],
                [$this->equalTo(Events::POST_BUILD_FIELD)],
                [$this->equalTo(Events::PRE_BUILD_FIELD)],
                [$this->equalTo(Events::POST_BUILD_FIELD)],
                [$this->equalTo(Events::POST_BUILD)]
            )
            ->willReturn(true);

        $this->dispatcher->expects($this->exactly(7))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_BUILD), $this->callback($callback[0])],
                [$this->equalTo(Events::PRE_BUILD_FIELD), $this->callback($callback[1])],
                [$this->equalTo(Events::BUILD_FIELD), $this->callback($callback[2])],
                [$this->equalTo(Events::POST_BUILD_FIELD), $this->callback($callback[1])],
                [$this->equalTo(Events::PRE_BUILD_FIELD), $this->callback($callback[1])],
                [$this->equalTo(Events::POST_BUILD_FIELD), $this->callback($callback[1])],
                [$this->equalTo(Events::POST_BUILD), $this->callback($callback[0])]
            )
            ->willReturnArgument(1);

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([
                $this->getAttribute('field', 'type', ['key' => 'value']),
                $this->getAttribute('field', 'type', ['key' => 'value']),
            ]);

        $this->type->expects($this->exactly(2))
            ->method('hasField')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($field);

        $this->getInstance()->buildForm($builder, ['key' => 'value', 'content_type' => $this->type]);
    }

    public function testBuildFormNoMetadata()
    {
        $this->dispatcher->expects($this->atLeastOnce())
            ->method('hasListeners')
            ->willReturn(false);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([]);

        $this->type->expects($this->once())
            ->method('getClass')
            ->willReturn('class');

        $builder = $this->getBuilder();
        $builder->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->buildForm($builder, ['content_type' => $this->type]);
    }

    public function testBuildFormNoMetadataEventDispatcher()
    {
        $builder = $this->getBuilder();

        $callback = function (BuilderEvent $argument) use ($builder) {
            self::assertSame($this->type, $argument->getContentType());
            self::assertSame($this->metadata, $argument->getMetadata());
            self::assertSame($builder, $argument->getBuilder());
            self::assertSame(['key' => 'value'], $argument->getOptions());

            return true;
        };

        $this->dispatcher->expects($this->exactly(2))
            ->method('hasListeners')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_BUILD)],
                [$this->equalTo(Events::POST_BUILD)]
            )
            ->willReturn(true);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_BUILD), $this->callback($callback)],
                [$this->equalTo(Events::POST_BUILD), $this->callback($callback)]
            )
            ->willReturnArgument(1);

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([]);

        $this->getInstance()->buildForm($builder, ['key' => 'value', 'content_type' => $this->type]);
    }

    public function testBuildFormIgnoreField()
    {
        $callback = function (FieldEvent $argument) {
            $argument->setIgnore(true);

            return true;
        };

        $this->dispatcher->expects($this->exactly(5))
            ->method('hasListeners')
            ->willReturnOnConsecutiveCalls(false, false, true, false, false);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::BUILD_FIELD), $this->callback($callback))
            ->willReturnArgument(1);

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field', 'type')]);

        $this->type->expects($this->once())
            ->method('hasField')
            ->willReturn(true);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($this->getField('field'));

        $builder = $this->getBuilder();

        $this->getInstance()->buildForm($builder, ['content_type' => $this->type]);
    }

    public function testBuildFormChangeField()
    {
        $callback = function (FieldEvent $argument) {
            $argument->getField()
                ->setType('type2')
                ->setOptions(['options2']);

            return true;
        };

        $this->dispatcher->expects($this->exactly(5))
            ->method('hasListeners')
            ->willReturnOnConsecutiveCalls(false, false, true, false, false);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::BUILD_FIELD), $this->callback($callback))
            ->willReturnArgument(1);

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field1', 'type1', ['options1'])]);

        $this->type->expects($this->once())
            ->method('hasField')
            ->willReturn(true);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($this->getField('field1', ['options1']));

        $builder = $this->getBuilder();
        $builder->expects($this->once())
            ->method('add')
            ->with($this->equalTo('field1'), $this->equalTo('type2'), $this->equalTo(['options2']));

        $this->getInstance()->buildForm($builder, ['content_type' => $this->type]);
    }

    public function testBuildView()
    {
        $view = $this->getView();
        $view->expects($this->never())
            ->method($this->anything());

        $form = $this->getForm();
        $form->expects($this->never())
            ->method($this->anything());

        $this->dispatcher->expects($this->once())
            ->method('hasListeners')
            ->willReturn(false);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->getInstance()->buildView($view, $form, ['content_type' => $this->type]);
    }

    public function testBuildViewEventDispatcher()
    {
        $view = $this->getView();
        $form = $this->getForm();

        $callback = function (ViewEvent $argument) use ($view, $form) {
            self::assertSame($this->type, $argument->getContentType());
            self::assertSame($this->metadata, $argument->getMetadata());
            self::assertSame($view, $argument->getView());
            self::assertSame($form, $argument->getForm());

            return true;
        };

        $this->dispatcher->expects($this->once())
            ->method('hasListeners')
            ->with($this->equalTo(Events::PRE_VIEW))
            ->willReturn(true);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::PRE_VIEW), $this->callback($callback));

        $this->getInstance()->buildView($view, $form, ['content_type' => $this->type]);
    }

    public function testFinishView()
    {
        $view = $this->getView();
        $view->expects($this->never())
            ->method($this->anything());

        $form = $this->getForm();
        $form->expects($this->never())
            ->method($this->anything());

        $this->dispatcher->expects($this->once())
            ->method('hasListeners')
            ->willReturn(false);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->getInstance()->finishView($view, $form, ['content_type' => $this->type]);
    }

    public function testFinishViewEventDispatcher()
    {
        $view = $this->getView();
        $form = $this->getForm();

        $callback = function (ViewEvent $argument) use ($view, $form) {
            self::assertSame($this->type, $argument->getContentType());
            self::assertSame($this->metadata, $argument->getMetadata());
            self::assertSame($view, $argument->getView());
            self::assertSame($form, $argument->getForm());

            return true;
        };

        $this->dispatcher->expects($this->once())
            ->method('hasListeners')
            ->with($this->equalTo(Events::POST_VIEW))
            ->willReturn(true);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::POST_VIEW), $this->callback($callback));

        $this->getInstance()->finishView($view, $form, ['content_type' => $this->type]);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->getResolver();

        $resolver
            ->expects($this->once())
            ->method('setRequired')
            ->with('content_type')
            ->willReturn($resolver)
        ;

        $resolver
            ->expects($this->once())
            ->method('setAllowedTypes')
            ->with('content_type', [ContentTypeInterface::class, 'string'])
            ->willReturn($resolver)
        ;

        $resolver
            ->expects($this->exactly(3))
            ->method('setNormalizer')
            ->willReturn($resolver)
        ;

        $this->getInstance()->configureOptions($resolver);
    }

    public function testGetParent()
    {
        self::assertEquals(FormType::class, $this->getInstance()->getParent());
    }

    public function testGetEventDispatcher()
    {
        self::assertSame($this->dispatcher, $this->getInstance()->getEventDispatcher());
    }

    public function testGetEventDispatcherDefault()
    {
        $this->dispatcher = null;

        self::assertInstanceOf(EventDispatcherInterface::class, $this->getInstance()->getEventDispatcher());
    }

    /**
     * @return ContentFormType
     */
    protected function getInstance()
    {
        return new ContentFormType($this->metadataFactory, $this->resolver, $this->dispatcher);
    }

    /**
     * @return FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBuilder()
    {
        return $this->createMock('Symfony\\Component\\Form\\FormBuilderInterface');
    }

    /**
     * @return FormView|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getView()
    {
        return $this->createMock('Symfony\\Component\\Form\\FormView');
    }

    /**
     * @return FormInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getForm()
    {
        return $this->createMock('Symfony\\Component\\Form\\FormInterface');
    }

    /**
     * @return OptionsResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver()
    {
        return $this->createMock('Symfony\\Component\\OptionsResolver\\OptionsResolver');
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return AttributeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAttribute($name, $type, array $options = [])
    {
        $mock = $this->createMock('Integrated\\Common\\Form\\Mapping\\AttributeInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        $mock->expects($this->any())
            ->method('getType')
            ->willReturn($type);

        $mock->expects($this->any())
            ->method('getOptions')
            ->willReturn($options);

        return $mock;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return ContentTypeFieldInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getField($name, array $options = [])
    {
        $mock = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeFieldInterface');
        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $mock->expects($this->any())
            ->method('getOptions')
            ->willReturn($options);

        return $mock;
    }
}
