<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config;

use Integrated\Common\Channel\ChannelInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigRepositoryInterface
{
    /**
     * Finds the object by its identifier.
     *
     * @param string $id
     *
     * @return ConfigInterface
     */
    public function find($id);

    /**
     * Finds all the managed objects.
     *
     * @return ConfigInterface[]
     */
    public function findAll();

    /**
     * Finds the objects by a set of criteria.
     *
     * @param array $criteria
     *
     * @return ConfigInterface[]
     */
    public function findBy(array $criteria);

    /**
     * Find the object by the given adaptor.
     *
     * @param string $criteria
     *
     * @return ConfigInterface[]
     */
    public function findByAdaptor($criteria);

    /**
     * Find the object by the given channel.
     *
     * @param string|ChannelInterface $criteria
     *
     * @return ConfigInterface[]
     */
    public function findByChannel($criteria);

    /**
     * Returns the class name of the object.
     *
     * @return string
     */
    public function getClassName();
}
