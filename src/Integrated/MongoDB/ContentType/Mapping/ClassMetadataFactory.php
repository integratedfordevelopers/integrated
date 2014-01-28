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

	/**
	 * When set to true then any call made to functions is from internal sources.
	 *
	 * @var bool
	 */
	private $internal = false;

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

	public function getAllMetadata()
	{
		$this->internal = true;

		$metadata = parent::getAllMetadata();

		$this->updateCache();

		$this->matcher->clearMatches();
		$this->internal = false;

		return $metadata;
	}

	public function getMetadataFor($className)
	{
		$metadata = parent::getMetadataFor($className);

		if ($this->internal) {
			return $metadata;
		}

		$this->internal = true;

		if ($this->matcher->hasMatches()) {
			parent::getAllMetadata();
		}

		$this->updateCache();

		$this->matcher->clearMatches();
		$this->internal = false;

		return $metadata;
	}

	private function updateCache()
	{
		if ($this->builder->hasChanges()) {
			$cache = $this->getCacheDriver();

			foreach ($this->builder->getChanges() as $meta) {
				$cache->save($meta->name . $this->cacheSalt, $meta, null);
			}
		}

		$this->builder->clearChanges();
	}
}