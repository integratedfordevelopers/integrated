<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType;

use Integrated\MongoDB\ContentType\DiscriminatorMapSubscriber;

use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapSubscriberTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ClassMetadataInfo
	 */
	private $parent;

	/**
	 * @var DiscriminatorMapSubscriber
	 */
	private $subscriber;

	protected function setUp()
	{
		$this->parent = new ClassMetadataInfo('stdClass');
		$this->subscriber = new DiscriminatorMapSubscriber('stdClass');
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->subscriber);
	}

	public function testGetClass()
	{
		$this->assertEquals('stdClass', $this->subscriber->getClass());
	}

	public function testGetSubscribedEvents()
	{
		$this->assertEquals(array(Events::loadClassMetadata), $this->subscriber->getSubscribedEvents());
	}

	public function testloadClassMetadata()
	{
		$this->parent->setDiscriminatorMap(array('test1' => 'stdClass', 'test2' => $this->getMockClass('stdClass')));

		$this->subscriber->loadClassMetadata($this->getEventArgs($this->parent));

		$this->assertEquals(array('stdClass' => 'stdClass'), $this->parent->discriminatorMap);
		$this->assertEquals('stdClass', $this->parent->discriminatorValue);
		$this->assertEmpty($this->parent->subClasses);
	}

	public function testloadClassMetadataWithParentSupperClass()
	{
		$this->parent->setDiscriminatorMap(array('test1' => 'stdClass'));
		$this->parent->isMappedSuperclass = true;

		$this->subscriber->loadClassMetadata($this->getEventArgs($this->parent));

		$this->assertEmpty($this->parent->discriminatorMap);
		$this->assertNull($this->parent->discriminatorValue);
		$this->assertEmpty($this->parent->subClasses);
	}

	public function testloadClassMetadataWithSubclass()
	{
		$child1 = $this->getMockClass('stdClass', array(), array(), 'stdClass_Mock_DiscriminatorMapSubscriberTest_child1');
		$child2 = $this->getMockClass('stdClass', array(), array(), 'stdClass_Mock_DiscriminatorMapSubscriberTest_child2');

		$meta1 = new ClassMetadataInfo($child1);
		$meta2 = new ClassMetadataInfo($child2);

		$this->subscriber->loadClassMetadata($this->getEventArgs($this->parent));
		$this->subscriber->loadClassMetadata($this->getEventArgs($meta1));
		$this->subscriber->loadClassMetadata($this->getEventArgs($meta2));

		$map = array(
			'stdClass' => 'stdClass',
			$child1 => $child1,
			$child2 => $child2
		);

		$this->assertEquals($map, $meta1->discriminatorMap);
		$this->assertEquals($map, $meta2->discriminatorMap);
	}

	public function testloadClassMetadataWithSubclassSupperClass()
	{
		$child1 = $this->getMockClass('stdClass', array(), array(), 'stdClass_Mock_DiscriminatorMapSubscriberTest_child1');
		$child2 = $this->getMockClass('stdClass', array(), array(), 'stdClass_Mock_DiscriminatorMapSubscriberTest_child2');

		$meta1 = new ClassMetadataInfo($child1);
		$meta1->isMappedSuperclass = true;

		$meta2 = new ClassMetadataInfo($child2);

		$this->subscriber->loadClassMetadata($this->getEventArgs($this->parent));
		$this->subscriber->loadClassMetadata($this->getEventArgs($meta1));
		$this->subscriber->loadClassMetadata($this->getEventArgs($meta2));

		$map = array(
			'stdClass' => 'stdClass',
			$child2 => $child2
		);

		$this->assertEquals($map, $meta1->discriminatorMap);
		$this->assertEquals($map, $meta2->discriminatorMap);
	}

	public function testloadClassMetadataWithInvalidClass()
	{
		$this->parent->setDiscriminatorMap(array('test1' => 'stdClass'));

		$this->subscriber->loadClassMetadata($this->getEventArgs($this->parent));
		$this->subscriber->loadClassMetadata($this->getEventArgs(new ClassMetadataInfo('ArrayObject')));

		$this->assertEquals(array('stdClass' => 'stdClass'), $this->parent->discriminatorMap);
	}

	/**
	 * @param ClassMetadataInfo $meta
	 * @return \Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getEventArgs(ClassMetadataInfo $meta)
	{
		$event = $this->getMock('Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs', array(), array(), '', false);
		$event->expects($this->atLeastOnce())
			->method('getClassMetadata')
			->will($this->returnValue($meta));

		return $event;
	}
}
 