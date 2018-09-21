<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Common\ContentType\Exception\ExceptionInterface;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\ContentType\Exception\UnexpectedTypeException;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBResolver implements ResolverInterface
{
    const CONTENT_TYPE_INTERFACE = 'Integrated\\Common\\ContentType\\ContentTypeInterface';

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * Content type caching array.
     *
     * @var array
     */
    protected $types = [];

    /**
     * Create a ContentTypeResolver based on a MongoDB DocumentRepository.
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
    public function getType($type)
    {
        if (!\is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        if (!isset($this->types[$type])) {
            if (null === ($document = $this->repository->findOneBy(['id' => $type]))) {
                throw new InvalidArgumentException(sprintf('Could not load content type bases on the given type "%s"', $type));
            }

            $this->types[$type] = $document;
        }

        return $this->types[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        try {
            $this->getType($type);
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
        return new MongoDBIterator($this->repository->findBy([], ['name' => 'ASC']));
    }
}
