<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping;

use Integrated\Common\ContentType\Mapping\Event\MetadataEvent;
use Integrated\Common\ContentType\Mapping\Metadata\ContentType;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataFactory implements MetadataFactoryInterface
{
	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher = null;

	/**
	 * @var DriverInterface
	 */
	private $driver;

	/**
	 * @var MetadataInterface[]
	 */
	protected $data = array();

	public function __construct(DriverInterface $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		if ($this->dispatcher === null) {
			$this->dispatcher = new EventDispatcher();
		}

		return $this->dispatcher;
	}

	/**
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return DriverInterface
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @inheritdoc
	 */
	public function getAllMetadata()
	{
		$metadata = [];

		foreach ($this->driver->getAllClassNames() as $class) {
			$data = $this->getMetadata($class);

			if ($data->isContent()) {
				$metadata[] = $data;
			}
		}

		return $metadata;
	}

	/**
	 * @inheritdoc
	 */
	public function getMetadata($class)
	{
		if (isset($this->data[$class])) {
			return $this->data[$class];
		}

		return $this->data[$class] = $this->loadMetadata($class);
	}

	/**
	 * @param string $class
	 * @return MetadataEditorInterface
	 */
	public function newMetadata($class)
	{
		return new ContentType($class);
	}

	/**
	 * @param string $class
	 * @return ContentType
	 */
	protected function loadMetadata($class)
	{
		$metadata = $this->newMetadata($class);

		if ($metadata->isContent())	{
			$this->driver->loadMetadataForClass($class, $metadata);
			$this->getEventDispatcher()->dispatch(Events::METADATA, new MetadataEvent($metadata));
		}

		return $metadata;
	}
}