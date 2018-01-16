<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\DataFixtures\MongoDB\Extension;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
trait ChannelExtension
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param string $id
     *
     * @return Channel
     *
     * @throws DocumentNotFoundException
     */
    public function channel($id)
    {
        $channel = $this->getContainer()
            ->get('doctrine_mongodb')
            ->getManager()
            ->getRepository(Channel::class)->find($id);

        if (!$channel) {
            throw DocumentNotFoundException::documentNotFound(Channel::class, $id);
        }

        return $channel;
    }
}
