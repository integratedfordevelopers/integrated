<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Cache;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\StorageBundle\Storage\Reflection\PropertyReflection;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ObjectCache implements ReflectionCacheInterface
{
    /**
     * @var ArrayCollection
     */
    private $cache;

    /**
     */
    public function __construct()
    {
        $this->cache = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyReflectionClass($class)
    {
        if (!$this->cache->contains($class)) {
            $reflection = new PropertyReflection($class);
            $this->cache->set($class, $reflection);
        }

        return $this->cache->get($class);
    }
}
