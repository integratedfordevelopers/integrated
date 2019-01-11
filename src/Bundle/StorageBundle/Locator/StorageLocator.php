<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Locator;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Cache\CacheInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageLocator extends FileLocator
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param KernelInterface $kernel
     * @param string|null     $path
     * @param array           $paths
     * @param CacheInterface  $cache
     */
    public function __construct(KernelInterface $kernel, $path, array $paths, CacheInterface $cache)
    {
        $this->cache = $cache;

        parent::__construct($kernel, $path, $paths);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        $this->cache = null;

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function locate($file, $currentPath = null, $first = true)
    {
        if ($file instanceof StorageInterface) {
            try {
                return $this->cache->path($file)->getPathname();
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('File not found.');
            }
        }

        // Continue the normal symfony stuff
        return parent::locate($file, $currentPath, $first);
    }
}
