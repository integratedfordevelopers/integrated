<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel;

use Integrated\Common\Content\Channel\ChannelInterface as ContentChannelInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ChannelManagerInterface
{
    /**
     * Create a channel object
     *
     * @return ChannelInterface
     */
    public function create();

    /**
     * Change or add the channel to the manager
     *
     * @param ChannelInterface $channel
     */
    public function persist(ContentChannelInterface $channel);

    /**
     * Remove the channel from the manager
     *
     * @param ChannelInterface $channel
     */
    public function remove(ContentChannelInterface $channel);

    /**
     * Delete all the managed channels.
     */
    public function clear();

    /**
     * Finds the user by its identifier.
     *
     * @param mixed $id
     *
     * @return ChannelInterface
     */
    public function find($id);

    /**
     * Finds all the managed channels.
     *
     * @return ChannelInterface[]
     */
    public function findAll();

    /**
     * Finds the first channel with a matching domain.
     *
     * @param string $criteria
     *
     * @return ChannelInterface
     */
    public function findByDomain($criteria);

    /**
     * Finds the channel by its name
     *
     * @param string $criteria
     *
     * @return ChannelInterface
     */
    public function findByName($criteria);

    /**
     * Finds the channel by a set of criteria.
     *
     * @param array $criteria
     *
     * @return ChannelInterface[]
     */
    public function findBy(array $criteria);

    /**
     * Returns the class name of the channel object
     *
     * @return string
     */
    public function getClassName();
}
