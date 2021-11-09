<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\GroupInterface;
use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupManager implements GroupManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(ObjectManager $om, $class)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($class);

        if (!is_subclass_of($this->repository->getClassName(), 'Integrated\\Bundle\\UserBundle\\Model\\GroupInterface')) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of Integrated\\Bundle\\UserBundle\\Model\\GroupInterface', $this->repository->getClassName()));
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
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClassName();

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function persist(GroupInterface $group, $flush = true)
    {
        $this->om->persist($group);

        if ($flush) {
            $this->om->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(GroupInterface $group, $flush = true)
    {
        $this->om->remove($group);

        if ($flush) {
            $this->om->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->om->clear($this->repository->getClassName());
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($criteria)
    {
        return $this->repository->findOneBy(['name' => $criteria]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }
}
