<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Extension\Subscriber;

use Integrated\Common\Content\Extension\Event\MetadataEvent;
use Integrated\Common\Content\Extension\Event\Subscriber\MetadataSubscriberInterface;
use Integrated\Common\Content\Extension\Events;
use Integrated\Common\Content\Extension\ExtensionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataSubscriber implements MetadataSubscriberInterface
{
	/**
	 * @var ExtensionInterface
	 */
	private $extension;

	/**
	 * @param ExtensionInterface $extension
	 */
	public function __construct(ExtensionInterface $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			Events::METADATA => 'process',
		];
	}

	public function getExtension()
	{
		return $this->extension;
	}

	public function process(MetadataEvent $event)
	{
		$metadata = $event->getMetadata();

		$attr = $metadata->newOption('workflow');
		$attr->setType('workflow_definition_choice');

		$metadata->addOption($attr);
	}
}