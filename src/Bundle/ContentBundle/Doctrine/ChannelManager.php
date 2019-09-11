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
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\Channel\ChannelManagerInterface;
use InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelManager implements ChannelManagerInterface
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

        if (!is_subclass_of($this->repository->getClassName(), 'Integrated\\Common\\Content\\Channel\\ChannelInterface')) {
            throw new InvalidArgumentException(sprintf('The class "%s" is not subclass of Integrated\\Common\\Content\\Channel\\ChannelInterface', $this->repository->getClassName()));
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
    public function persist(ChannelInterface $channel, $flush = true)
    {
        $this->om->persist($channel);

        if ($flush) {
            $this->om->flush($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ChannelInterface $channel, $flush = true)
    {
        $this->om->remove($channel);

        if ($flush) {
            $this->om->flush($channel);
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
        return $this->repository->findBy([], ['name' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByDomain($criteria)
    {
        $channel = $this->repository->findOneBy(['domains' => $criteria]);
        if (!$channel) {
            //find a fallback with/without www.
            $channel = $this->repository->findOneBy(
                ['domains' => (stripos($criteria, 'www.')) ? str_ireplace('www.', '', $criteria) : 'www.'.$criteria]
            );
        }

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($criteria)
    {
        return $this->repository->findOneBy(['shortName' => $criteria]);
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
