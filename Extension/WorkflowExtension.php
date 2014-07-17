<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Extension;

use Integrated\Bundle\WorkflowBundle\Extension\Subscriber\MetadataSubscriber;

use Integrated\Common\Content\Extension\ExtensionInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowExtension implements ExtensionInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container = null;

	/**
	 * @inheritdoc
	 */
	public function getSubscribers()
	{
		return [
			new MetadataSubscriber($this)
		];
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated.extension.workflow';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}