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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\ScopeInterface;
use Integrated\Bundle\UserBundle\Model\ScopeManagerInterface;
use InvalidArgumentException;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class ScopeManager implements ScopeManagerInterface
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
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($class);

        if (!is_subclass_of($this->repository->getClassName(), 'Integrated\\Bundle\\UserBundle\\Model\\ScopeInterface')) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of Integrated\\Bundle\\UserBundle\\Model\\ScopeInterface', $this->repository->getClassName()));
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
    public function persist(ScopeInterface $scope, $flush = true)
    {
        $this->om->persist($scope);

        if ($flush) {
            $this->om->flush($scope);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ScopeInterface $scope, $flush = true)
    {
        $this->om->remove($scope);

        if ($flush) {
            $this->om->flush($scope);
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
        return $this->repository->findBy(['admin' => false]);
    }

    /**
     * @param $name
     *
     * @return ScopeInterface|null
     */
    public function findByName($name)
    {
        return $this->repository->findOneBy(['name' => $name]);
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
