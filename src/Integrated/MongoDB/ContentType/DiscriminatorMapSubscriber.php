<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\ContentType;

use Integrated\MongoDb\ContentType\Exception\InvalidArgumentException;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\Common\EventSubscriber;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapSubscriber implements EventSubscriber
{
	private $class;

	/**
	 * @var ClassMetadataInfo
	 */
	private $parent;

	/**
	 * @var ClassMetadataInfo[]
	 */
	private $children = array();

	/**
	 * Automatically build the discriminator map for the given class
	 *
	 * @param string $class
	 *
	 * @throws InvalidArgumentException if the class does not exist
	 */
	public function __construct($class)
	{
		if (!class_exists($class)) {
			throw new InvalidArgumentException(sprintf('The class "%s" does not exist', $class));
		}

		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSubscribedEvents()
	{
		return array(
			Events::loadClassMetadata
		);
	}

	/**
	 * Automatically create the discriminator map for the given parent class.
	 * The discriminator value will be set as the class name of the document.
	 *
	 * @param LoadClassMetadataEventArgs $event
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $event)
	{
		$class = $event->getClassMetadata();

		if (!is_a($class->getName(), $this->class, true)) {
			return;
		}

		if ($class->getName() == $this->class) {
			$this->setParent($class);
		} else {
			$this->addChild($class);
		}
	}

	/**
	 * @param ClassMetadataInfo $parent
	 */
	private function setParent(ClassMetadataInfo $parent)
	{
		$this->parent = $parent;

		// Reset discriminator and subclasses config as this will be build automatically
		// NOTE: doctrine doc comments claim these properties are read only

		$this->parent->discriminatorMap = array();
		$this->parent->discriminatorValue = null;

		$this->parent->subClasses = array();

		// The parent can be in its own discriminator map if it is not a mapped super class

		$this->addDiscriminator($this->parent);
	}

	/**
	 * @param ClassMetadataInfo $child
	 */
	private function addChild(ClassMetadataInfo $child)
	{
		$this->addDiscriminator($child);

		// every time a child is added the map need to be distribute to all the children

		$this->children[] = $child;

		foreach ($this->children as $child) {
			$child->setDiscriminatorMap($this->parent->discriminatorMap);
		}
	}

	/**
	 * @param ClassMetadataInfo $class
	 */
	private function addDiscriminator(ClassMetadataInfo $class)
	{
		if ($class->isMappedSuperclass) {
			return;
		}

		$this->parent->setDiscriminatorMap(array($class->getName() => $class->getName())); // it's called set but is implemented as a add.
	}
}