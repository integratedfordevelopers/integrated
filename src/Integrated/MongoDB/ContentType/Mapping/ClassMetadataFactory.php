<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\ContentType\Mapping;

use Integrated\MongoDB\ContentType\DiscriminatorMapBuilderSubscriber;
use Integrated\MongoDB\ContentType\ClassMetadataLoadFinderSubscriber;

use Doctrine\Common\EventManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassMetadataFactory extends BaseClassMetadataFactory
{
	/**
	 * @var DocumentManager
	 */
	private $dm;

	/**
	 * @var EventManager
	 */
	private $evm;

	/**
	 * @var ClassMetadataLoadFinderSubscriber
	 */
	private $matcher;

	/**
	 * @var DiscriminatorMapBuilderSubscriber
	 */
	private $builder;

	public function __construct()
	{
		$this->matcher = new ClassMetadataLoadFinderSubscriber();
		$this->builder = new DiscriminatorMapBuilderSubscriber($this);
	}

	public function addManagedClass($class)
	{
		$this->matcher->addClass($class);
		$this->builder->addClass($class);
	}

	public function setDocumentManager(DocumentManager $dm)
	{
		if ($this->dm) {
			$this->evm->removeEventSubscriber($this->matcher);
			$this->evm->removeEventSubscriber($this->builder);

			$this->evm = null;
		}

		$this->dm = $dm;

		parent::setDocumentManager($dm);

		$this->evm = $this->dm->getEventManager();

		$this->evm->addEventSubscriber($this->matcher);
		$this->evm->addEventSubscriber($this->builder);
	}

	public function getMetadataFor($className)
	{
		$meta = parent::getMetadataFor($className);

		if ($this->matcher->hasMatches()) {
			$this->getAllMetadata();
		}

		$this->matcher->clearMatches();

		// update the cache if required

		if ($this->builder->hasChanges()) {
			$cache = $this->getCacheDriver();

			foreach ($this->builder->getChanges() as $meta) {
				$cache->save($meta->name . $this->cacheSalt, $meta, null);
			}
		}

		$this->builder->clearChanges();

		return $meta;
	}
}