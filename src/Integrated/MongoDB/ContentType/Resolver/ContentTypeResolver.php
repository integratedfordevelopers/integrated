<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\Contenttype\Resolver;

use Integrated\Common\ContentType\Resolver\ContentTypeResolverListInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeResolver implements ContentTypeResolverListInterface
{
	protected $repository;

	/**
	 * Create a ContentTypeResolver based on a MongoDB DocumentRepository
	 *
	 * The DocumentRepository should be of a class that implements ContentTypeInterface
	 * or else the ContentTypeResolver will throw a exception.
	 *
	 * @param DocumentRepository $repository
	 */
	public function __construct(DocumentRepository $repository)
	{
		$reflection = new \ReflectionClass($repository->getClassName());

		if (!$reflection->implementsInterface('Integrated\Component\Content\ContentTypeInterface')) {
			throw new \Exception(); // @todo good exception
		}

		$this->repository = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType($class, $type)
	{
		if (null !== ($obj = $this->repository->findOneBy(['class' => $class, 'type' => $type]))) {
			return $obj;
		}

		throw new \Exception(); // @todo good exception
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasType($class, $type)
	{
		return (bool) $this->repository->findBy(['class' => $class, 'type' => $type])->count();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTypes()
	{
		return new ContentTypeIterator($this->repository->findAll());
	}
}