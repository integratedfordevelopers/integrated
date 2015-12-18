<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Decision
{
    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @var array
     */
    protected $decisionMap;

    /**
     * @param FilesystemRegistry $registry
     * @param array $decisionMap
     */
    public function __construct(FilesystemRegistry $registry, array $decisionMap)
    {
        $this->registry = $registry;
        $this->decisionMap = $decisionMap;
    }

    /**
     * @param $class
     * @return array
     */
    public function getFilesystems($class)
    {
        $className = get_class($class);
        if (isset($this->decisionMap[$className])) {
            return new ArrayCollection(array_values($this->decisionMap[$className]));
        }

        return new ArrayCollection($this->registry->keys());
    }
}
