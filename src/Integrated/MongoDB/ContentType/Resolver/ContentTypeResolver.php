<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\ContentType\Resolver;

use Integrated\Common\ContentType\Resolver\ContentTypeResolverListInterface;
use Integrated\MongoDB\ContentType\Exception\ExceptionInterface;
use Integrated\MongoDB\ContentType\Exception\InvalidArgumentException;
use Integrated\MongoDB\ContentType\Exception\UnexpectedTypeException;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeResolver implements ContentTypeResolverListInterface
{
	const CONTENT_TYPE_INTERFACE = 'Integrated\Common\ContentType\ContentTypeInterface';

	/**
	 * @var DocumentRepository
	 */
	protected $repository;

	/**
	 * @var array
	 */
	protected $types = array();

	/**
	 * Create a ContentTypeResolver based on a MongoDB DocumentRepository
	 *
	 * The DocumentRepository should be of a class that implements ContentTypeInterface
	 * or else the ContentTypeResolver will throw a exception.
	 *
	 * @param DocumentRepository $repository
	 *
	 * @throws InvalidArgumentException if the document class does implement the correct interface
	 */
	public function __construct(DocumentRepository $repository)
	{
		$reflection = new \ReflectionClass($repository->getClassName());

		if (!$reflection->implementsInterface(self::CONTENT_TYPE_INTERFACE)) {
			throw new InvalidArgumentException(sprintf('The document class "%s" of the DocumentRepository does not implement the "%s" interface.', $repository->getClassName(), self::CONTENT_TYPE_INTERFACE));
		}

		$this->repository = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType($class, $type)
	{
		if (!is_string($class)) {
			throw new UnexpectedTypeException($class, 'string');
  		}

		if (!is_string($type)) {
			throw new UnexpectedTypeException($type, 'string');
  		}

		$key = json_encode(['class' => $class, 'type' => $type]);

		if (!isset($this->types[$key])) {
			if (null === ($type = $this->repository->findOneBy(['class' => $class, 'type' => $type]))) {
				throw new InvalidArgumentException(sprintf('Could not load content type bases on the given class "%s" and type "%s"', $class, $type));
			}

			$this->types[$key] = $type;
		}

		return $this->types[$key];
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasType($class, $type)
	{
		try {
			$this->getType($class, $type);
		} catch (UnexpectedTypeException $e) {
			throw $e;
		} catch (ExceptionInterface $e) {
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTypes()
	{
		return new ContentTypeIterator($this->repository->findAll());
	}
}