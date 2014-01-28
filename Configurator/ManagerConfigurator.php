<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Configurator;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ManagerConfigurator
{
	/**
	 * @var callable
	 */
	private $originalConfigurator;

	public function __construct(callable $originalConfigurator = null)
	{
		$this->originalConfigurator = $originalConfigurator;
	}

	public function configure(DocumentManager $manager)
	{
		call_user_func($this->originalConfigurator, $manager);

		// TODO: make it configurable

		$manager->getMetadataFactory()->addManagedClass('Integrated\Bundle\ContentBundle\Document\Content\Content');
	}
} 