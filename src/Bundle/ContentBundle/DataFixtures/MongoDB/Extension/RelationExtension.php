<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension;

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait RelationExtension
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param string $id
     *
     * @return null|Relation
     */
    public function relation($id)
    {
        return $this->getContainer()
            ->get('doctrine_mongodb')
            ->getManager()
            ->getRepository(Relation::class)->find($id)
        ;
    }
}
