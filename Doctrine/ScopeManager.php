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
use Doctrine\Common\Persistence\ObjectRepository;

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
     * @param $class
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
     * @return string
     */
    public function create()
    {
        $class = $this->getClassName();
        return new $class();
    }

    /**
     * @param ScopeInterface $scope
     * @param bool $flush
     */
    public function persist(ScopeInterface $scope, $flush = true)
    {
        $this->om->persist($scope);

        if ($flush) {
            $this->om->flush($scope);
        }
    }

    /**
     * @param ScopeInterface $scope
     * @param bool $flush
     */
    public function remove(ScopeInterface $scope, $flush = true)
    {
        $this->om->remove($scope);

        if ($flush) {
            $this->om->flush($scope);
        }
    }

    public function clear()
    {
        $this->om->clear($this->repository->getClassName());
    }

    /**
     * @param mixed $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param $name
     * @return null|object
     */
    public function findByName($name)
    {
        return $this->repository->findOneBy(['name' => $name]);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }
}
