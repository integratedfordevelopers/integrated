<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Model;

use Doctrine\ORM\EntityRepository;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Connector\Config\ConfigManagerInterface;
use InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigRepository extends EntityRepository implements ConfigManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->_class->getReflectionClass()->newInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ConfigInterface $object, $flush = true)
    {
        if (!$this->_class->getReflectionClass()->isInstance($object)) {
            throw new InvalidArgumentException(
                sprintf('The object (%s) is not a instance of %s', \get_class($object), $this->getClassName())
            );
        }

        $this->_em->persist($object);

        if ($flush) {
            $this->_em->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ConfigInterface $object, $flush = true)
    {
        if (!$this->_class->getReflectionClass()->isInstance($object)) {
            throw new InvalidArgumentException(
                sprintf('The object (%s) is not a instance of %s', \get_class($object), $this->getClassName())
            );
        }

        $this->_em->remove($object);

        if ($flush) {
            $this->_em->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findByAdaptor($criteria)
    {
        return $this->findBy([
            'adaptor' => $criteria,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByChannel($criteria)
    {
        if ($criteria instanceof ChannelInterface) {
            $criteria = $criteria->getId();
        }

        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('r')
            ->where($expr->like('r.channels', $expr->literal('%'.json_encode($criteria).'%')))
            ->getQuery()
            ->getResult();
    }
}
