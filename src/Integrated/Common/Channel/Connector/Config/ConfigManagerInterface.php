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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigManagerInterface extends ConfigRepositoryInterface
{
    /**
   	 * Create a user object
   	 *
   	 * @return ConfigInterface
   	 */
   	public function create();

    /**
   	 * Change or add the user to the manager
   	 *
   	 * @param ConfigInterface $object
   	 */
   	public function persist(ConfigInterface $object);

    /**
   	 * Remove the user from the manager
   	 *
   	 * @param ConfigInterface $object
   	 */
   	public function remove(ConfigInterface $object);

    /**
   	 * Delete all the managed objects.
   	 */
   	public function clear();
}
