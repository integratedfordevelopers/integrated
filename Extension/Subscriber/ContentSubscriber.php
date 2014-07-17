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

use Integrated\Common\Content\Extension\Event\Subscriber\ContentSubscriberInterface;

use Integrated\Common\Content\Extension\Events;
use Integrated\Common\Content\Extension\ExtensionInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentSubscriber implements ContentSubscriberInterface
{
	const CONTENT_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Relation\\Relation';

	/**
	 * @var ExtensionInterface
	 */
	private $extension;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ExtensionInterface $extension
	 * @param ContainerInterface $container
	 */
	public function __construct(ExtensionInterface $extension, ContainerInterface $container)
	{
		$this->extension = $extension;
		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
//			Events::POST_READ   => 'read',
//			Events::POST_CREATE => 'update',
//			Events::PRE_UPDATE  => 'update',
//			Events::POST_DELETE => 'delete'
		];
	}

	/**
	 * @return ExtensionInterface
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @return ContainerInterface
	 */
	protected function getContainer()
	{
		return $this->container;
	}
}