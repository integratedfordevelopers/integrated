<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\ContentType\Iterator;
use Integrated\Common\ContentType\Resolver\PriorityResolver;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeManager
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var ContentTypeInterface[]
     */
    private $contentTypes;

    /**
     * @param ResolverInterface $resolver
     * @param ObjectManager     $om
     * @param $class
     */
    public function __construct(ResolverInterface $resolver, ObjectManager $om, $class)
    {
        $this->resolver = $resolver;
        $this->om = $om;
        $this->repository = $this->om->getRepository($class);

        if (!is_subclass_of($this->repository->getClassName(), ContentTypeInterface::class)) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of %s', $this->repository->getClassName(), ContentTypeInterface::class));
        }
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->om;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param string $className
     *
     * @return ContentTypeInterface[]
     */
    public function filterInstanceOf($className)
    {
        $contentTypes = [];

        foreach ($this->getAll() as $contentType) {
            if (is_a($contentType->getClass(), $className, true)) {
                $contentTypes[] = $contentType;
            }
        }

        return $contentTypes;
    }

    /**
     * @return \Integrated\Common\ContentType\IteratorInterface|ContentTypeInterface[]
     */
    public function getAll()
    {
        if (!$this->resolver instanceof PriorityResolver) {
            return $this->resolver->getTypes();
        }

        if (null === $this->contentTypes) {
            $contentTypes = [];

            foreach ($this->resolver->getResolvers() as $resolver) {
                $contentTypes = array_merge(iterator_to_array($resolver->getTypes()), $contentTypes);
            }
            sort($contentTypes);
            $this->contentTypes = new Iterator($contentTypes);
        }

        return $this->contentTypes;
    }

    /**
     * @param string $type
     *
     * @return ContentTypeInterface
     */
    public function getType($type)
    {
        return $this->resolver->getType($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasType($type)
    {
        return $this->resolver->hasType($type);
    }
}
