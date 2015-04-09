<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form;

use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Event\OptionsEvent;
use Integrated\Common\Content\Form\Event\ViewEvent;
use Integrated\Common\Content\Form\Events;
use Integrated\Common\Content\Form\FormType;

use Integrated\Common\ContentType\ContentTypeFieldInterface;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Mapping\AttributeInterface;
use Integrated\Common\ContentType\Mapping\MetadataInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use stdClass;

/**
 * @covers Integrated\Common\Content\Form\FormType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormTypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $type;

    /**
     * @var MetadataInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

	/**
	 * @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $dispatcher;

	protected function setUp()
	{
        $this->type = $this->getMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $this->metadata = $this->getMock('Integrated\\Common\\ContentType\\Mapping\\MetadataInterface');
        $this->dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\\Common\\Content\\Form\\FormTypeInterface', $this->getInstance());
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
                $this->getAttribute('field1'),
                $this->getAttribute('field2'),
                $this->getAttribute('field3')
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
                $this->getField('field1', 'type1', ['options1']),
                $this->getField('field3', 'type3', ['options3'])
            );

        $builder = $this->getBuilder();
        $builder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo('field1'), $this->equalTo('type1'), $this->equalTo(['options1'])],
                [$this->equalTo('field3'), $this->equalTo('type3'), $this->equalTo(['options3'])]
            );

        $this->getInstance()->buildForm($builder, []);
    }

    public function testBuildFormEventDispatcher()
    {
        $builder = $this->getBuilder();
        $field = $this->getField('field', 'type');

        $callback = [
            function(BuilderEvent $argument) use ($builder) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame($builder, $argument->getBuilder());
                self::assertSame(['key' => 'value'], $argument->getOptions());

                return true;
            },
            function(BuilderEvent $argument) use ($builder) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame($builder, $argument->getBuilder());
                self::assertSame(['key' => 'value'], $argument->getOptions());
                self::assertSame('field', $argument->getField());

                return true;
            },
            function(FieldEvent $argument) use ($builder, $field) {
                self::assertSame($this->type, $argument->getContentType());
                self::assertSame($this->metadata, $argument->getMetadata());
                self::assertSame(['key' => 'value'], $argument->getOptions());
                self::assertNotSame($field, $argument->getField());
                self::assertEquals($field, $argument->getField());

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
            );

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field'), $this->getAttribute('field')]);

        $this->type->expects($this->exactly(2))
            ->method('hasField')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($field);

        $this->getInstance()->buildForm($builder, ['key' => 'value']);
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

        $this->type->expects($this->never())
            ->method($this->anything());

        $builder = $this->getBuilder();
        $builder->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->buildForm($builder, []);
    }

    public function testBuildFormNoMetadataEventDispatcher()
    {
        $builder = $this->getBuilder();

        $callback = function(BuilderEvent $argument) use ($builder) {
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
            );

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([]);

        $this->getInstance()->buildForm($builder, ['key' => 'value']);
    }

    /**
     * @dataProvider buildFormChangeOptionsProvider
     */
    public function testBuildFormChangeOptions()
    {
        $callback = func_get_args();

        $this->dispatcher->expects($this->exactly(5))
            ->method('hasListeners')
            ->willReturn(true);

        $this->dispatcher->expects($this->exactly(5))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_BUILD), $this->callback($callback[0])],
                [$this->equalTo(Events::PRE_BUILD_FIELD), $this->callback($callback[1])],
                [$this->equalTo(Events::BUILD_FIELD), $this->callback($callback[2])],
                [$this->equalTo(Events::POST_BUILD_FIELD), $this->callback($callback[3])],
                [$this->equalTo(Events::POST_BUILD), $this->callback($callback[4])]
            );

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field')]);

        $this->type->expects($this->once())
            ->method('hasField')
            ->willReturn(true);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($this->getField('field', 'type'));

        $this->getInstance()->buildForm($this->getBuilder(), []);
    }

    public function buildFormChangeOptionsProvider()
    {
        // PHPUnit calls the callback twice, once when running the test and once again after the
        // test is done. Because the argument is saved we have to choose between changing the
        // argument or asserting its value. So to check every possible combination we have to run
        // the test five times with different callback checks.

        return [
            [
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(FieldEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                }
            ],
            [
                function(BuilderEvent $argument) {
                    $argument->setOptions(['key' => 'value']);
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame(['key' => 'value'], $argument->getOptions());
                    return true;
                },
                function(FieldEvent $argument) {
                    self::assertSame(['key' => 'value'], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame(['key' => 'value'], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame(['key' => 'value'], $argument->getOptions());
                    return true;
                }
            ],
            [
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    $argument->setOptions(['key' => 'value']);
                    return true;
                },
                function(FieldEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                }
            ],
            [
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(FieldEvent $argument) {
                    $argument->setOptions(['key' => 'value']);
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                }
            ],
            [
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(FieldEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                },
                function(BuilderEvent $argument) {
                    $argument->setOptions(['key' => 'value']);
                    return true;
                },
                function(BuilderEvent $argument) {
                    self::assertSame([], $argument->getOptions());
                    return true;
                }
            ],
        ];
    }

    public function testBuildFormIgnoreField()
    {
        $callback = function(FieldEvent $argument) {
            $argument->setIgnore(true);
            return true;
        };

        $this->dispatcher->expects($this->exactly(5))
            ->method('hasListeners')
            ->willReturnOnConsecutiveCalls(false, false, true, false, false);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::BUILD_FIELD), $this->callback($callback));

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field')]);

        $this->type->expects($this->once())
            ->method('hasField')
            ->willReturn(true);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($this->getField('field', 'type'));

        $builder = $this->getBuilder();
        $builder->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->buildForm($builder, []);
    }

    public function testBuildFormChangeField()
    {
        $callback = function(FieldEvent $argument) {
            $argument->setField($this->getField('field2', 'type2', ['options2']));
            return true;
        };

        $this->dispatcher->expects($this->exactly(5))
            ->method('hasListeners')
            ->willReturnOnConsecutiveCalls(false, false, true, false, false);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::BUILD_FIELD), $this->callback($callback));

        $this->metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([$this->getAttribute('field1')]);

        $this->type->expects($this->once())
            ->method('hasField')
            ->willReturn(true);

        $this->type->expects($this->once())
            ->method('getField')
            ->willReturn($this->getField('field1', 'type1', ['options1']));

        $builder = $this->getBuilder();
        $builder->expects($this->once())
            ->method('add')
            ->with($this->equalTo('field1'), $this->equalTo('type2'), $this->equalTo(['options2']));

        $this->getInstance()->buildForm($builder, []);
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

        $this->getInstance()->buildView($view, $form, []);
    }

    public function testBuildViewEventDispatcher()
    {
        $view = $this->getView();
        $form = $this->getForm();

        $callback = function(ViewEvent $argument) use ($view, $form) {
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

        $this->getInstance()->buildView($view, $form, []);
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

        $this->getInstance()->finishView($view, $form, []);
    }

    public function testFinishViewEventDispatcher()
    {
        $view = $this->getView();
        $form = $this->getForm();

        $callback = function(ViewEvent $argument) use ($view, $form) {
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

        $this->getInstance()->finishView($view, $form, []);
    }

    public function testSetDefaultOptions()
    {
        $this->dispatcher->expects($this->exactly(2))
            ->method('hasListeners')
            ->willReturn(false);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $object = new stdClass();

        $this->type->expects($this->once())
            ->method('getClass')
            ->willReturn('the-class-name');

//        $this->type->expects($this->once())
//            ->method('create')
//            ->willReturn($object);

        $callback = function($argument) use ($object) {
            self::assertEquals('the-class-name', $argument['data_class']);

// this give a problem as seams phpunit checks arguments 2 times
//            self::assertSame($object, $argument['empty_data']($this->getForm()));

            return true;
        };

        $resolver = $this->getResolver();
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->callback($callback));

        $this->getInstance()->setDefaultOptions($resolver);
    }

    public function testSetDefaultOptionsEventDispatcher()
    {
        $resolver = $this->getResolver();

        $callback = function(OptionsEvent $argument) use ($resolver) {
            self::assertSame($this->type, $argument->getContentType());
            self::assertSame($this->metadata, $argument->getMetadata());
            self::assertSame($resolver, $argument->getResolver());

            return true;
        };

        $this->dispatcher->expects($this->exactly(2))
            ->method('hasListeners')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_OPTIONS)],
                [$this->equalTo(Events::POST_OPTIONS)]
            )
            ->willReturn(true);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_OPTIONS), $this->callback($callback)],
                [$this->equalTo(Events::POST_OPTIONS), $this->callback($callback)]
            );

        $this->getInstance()->setDefaultOptions($resolver);
    }

    public function testGetType()
    {
        self::assertSame($this->type, $this->getInstance()->getType());
    }

	public function testGetParent()
	{
		self::assertEquals('form', $this->getInstance()->getParent());
	}

    /**
     * @dataProvider getNameProvider
     */
    public function testGetName($name, $expected)
    {
        $this->type->expects($this->once())
            ->method('getType')
            ->willReturn($name);

        $type = $this->getInstance();

        self::assertEquals('integrated_content_form_' . $expected, $type->getName());
        self::assertEquals('integrated_content_form_' . $expected, $type->getName());
    }

    public function getNameProvider()
    {
        return [
            ['this-is-the-type-name1', 'this-is-the-type-name1'],
            ['This-Is-The-Type-Name2', 'this-is-the-type-name2'],
            ['this_is_the_type_name3', 'this_is_the_type_name3'],
            ['this#is#the#type#name4', 'thisisthetypename4'],
            ['This Is The Type Name5', 'thisisthetypename5'],
        ];
    }

    public function testGetEventDispatcher()
    {
        self::assertSame($this->dispatcher, $this->getInstance()->getEventDispatcher());
    }

    public function testGetEventDispatcherDefault()
    {
        $this->dispatcher = null;

        self::assertInstanceOf('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface', $this->getInstance()->getEventDispatcher());
    }

    /**
     * @return FormType
     */
    protected function getInstance()
    {
        return new FormType($this->type, $this->metadata, $this->dispatcher);
    }

    /**
     * @return FormBuilderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBuilder()
    {
        return $this->getMock('Symfony\\Component\\Form\\FormBuilderInterface');
    }

    /**
     * @return FormView | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getView()
    {
        return $this->getMock('Symfony\\Component\\Form\\FormView');
    }

    /**
     * @return FormInterface |  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getForm()
    {
        return $this->getMock('Symfony\\Component\\Form\\FormInterface');
    }

    /**
     * @return OptionsResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver()
    {
        return $this->getMock('Symfony\\Component\\OptionsResolver\\OptionsResolverInterface');
    }

    /**
     * @param string $name
     *
     * @return AttributeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAttribute($name)
    {
        $mock = $this->getMock('Integrated\\Common\\ContentType\\Mapping\\AttributeInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return ContentTypeFieldInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getField($name, $type, array $options = [])
    {
        $mock = $this->getMock('Integrated\\Common\\ContentType\\ContentTypeFieldInterface');
        $mock->expects($this->any())
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
}
