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

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var ContentType[]|null
     */
    protected $contentTypes = null;

    /**
     * ContentTypeManager constructor.
     * @param ObjectManager $om
     * @param $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($class);

        if (!is_subclass_of($this->repository->getClassName(), 'Integrated\\Common\\ContentType\\ContentTypeInterface')) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of Integrated\\Common\\ContentType\\ContentTypeInterface', $this->repository->getClassName()));
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
     * @param $className
     * @return ContentType[]
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
     * @return ContentType[]
     */
    public function getAll()
    {
        if (null === $this->contentTypes) {
            $this->contentTypes = $this->repository->findBy([], ['name' => 'ASC']);
        }

        return $this->contentTypes;
    }
}