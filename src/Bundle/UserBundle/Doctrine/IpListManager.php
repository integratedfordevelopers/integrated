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

use Darsyn\IP\Version\Multi as IP;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\IpList;
use Integrated\Bundle\UserBundle\Model\IpListManagerInterface;

class IpListManager implements IpListManagerInterface
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

        if (!is_a($this->repository->getClassName(), IpList::class, true)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" is not a instance of %s', $this->repository->getClassName(), IpList::class));
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

    public function create(IP $ip, string $description)
    {
        $class = $this->getClassName();

        return new $class($ip, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(IpList $list, $flush = true)
    {
        $this->om->persist($list);

        if ($flush) {
            $this->om->flush($list);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(IpList $list, $flush = true)
    {
        $this->om->remove($list);

        if ($flush) {
            $this->om->flush($list);
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
